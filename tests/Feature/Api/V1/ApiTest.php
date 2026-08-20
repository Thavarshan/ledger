<?php

namespace Tests\Feature\Api\V1;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_existing_users_can_login_and_receive_a_scoped_token(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Test phone',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.id', $user->id);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_invalid_credentials_are_rejected_without_token_creation(): void
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.auth.login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
            'device_name' => 'Test phone',
        ])->assertStatus(422)->assertJsonMissingPath('data.token');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_two_factor_users_must_supply_a_second_factor(): void
    {
        $user = User::factory()->withTwoFactor()->create();

        $this->postJson(route('api.v1.auth.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Test phone',
        ])->assertStatus(409)->assertJsonPath('code', 'two_factor_required');
    }

    public function test_recovery_codes_are_consumed_once_when_logging_in(): void
    {
        $user = User::factory()->withTwoFactor()->create();
        $credentials = [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Test phone',
            'recovery_code' => 'recovery-code-1',
        ];

        $this->postJson(route('api.v1.auth.login'), $credentials)->assertOk();
        $this->postJson(route('api.v1.auth.login'), $credentials)->assertStatus(422);
    }

    public function test_account_creation_is_idempotent_and_returns_safe_data(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test', ['accounts:read', 'accounts:write'])->plainTextToken;
        $payload = [
            'name' => 'Primary account',
            'account_type' => 'savings',
            'bank_name' => 'Bank of Ceylon',
            'country_code' => 'LK',
            'currency_code' => 'LKR',
            'account_number' => '001234567890',
        ];
        $headers = [
            'Authorization' => 'Bearer '.$token,
            'Idempotency-Key' => 'account-create-1',
        ];

        $first = $this->postJson(route('api.v1.accounts.store'), $payload, $headers);
        $second = $this->postJson(route('api.v1.accounts.store'), $payload, $headers);

        $first->assertCreated()
            ->assertJsonPath('data.balance_minor', '0')
            ->assertJsonMissingPath('data.account_number')
            ->assertHeader('Location');
        $second->assertCreated()->assertHeader('Idempotent-Replayed', 'true');
        $this->assertDatabaseCount('accounts', 1);
    }

    public function test_account_creation_requires_idempotency_and_write_ability(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-only', ['accounts:read'])->plainTextToken;

        $this->asApiToken($token)
            ->postJson(route('api.v1.accounts.store'), [])
            ->assertForbidden();

        $writeToken = $user->createToken('write', ['accounts:write'])->plainTextToken;
        $response = $this->asApiToken($writeToken)
            ->postJson(route('api.v1.accounts.store'), []);
        $response->assertStatus(400)
            ->assertJsonPath('code', 'idempotency_key_required');
    }

    public function test_transaction_responses_include_the_safe_nested_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $transaction = Transaction::factory()->forAccount($account)->create();
        $token = $user->createToken('test', ['transactions:read'])->plainTextToken;

        $this->asApiToken($token)
            ->getJson(route('api.v1.transactions.show', $transaction))
            ->assertOk()
            ->assertJsonPath('data.account.id', $account->id)
            ->assertJsonPath('data.account.currency_code', 'LKR')
            ->assertJsonMissingPath('data.account.iban');
    }

    public function test_profile_and_token_management_revoke_tokens_after_password_change(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('mobile', [
            'profile:read', 'profile:write', 'tokens:manage',
        ])->plainTextToken;

        $this->asApiToken($token)
            ->getJson(route('api.v1.me.show'))
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);

        $this->asApiToken($token)
            ->postJson(route('api.v1.tokens.store'), [
                'name' => 'External integration',
                'abilities' => ['profile:read'],
                'expires_in_days' => 30,
                'current_password' => 'password',
            ])
            ->assertCreated()
            ->assertJsonPath('data.token_type', 'Bearer');

        $this->asApiToken($token)
            ->putJson(route('api.v1.me.password'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertNoContent();

        $this->asApiToken($token)
            ->getJson(route('api.v1.me.show'))
            ->assertUnauthorized();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_non_owner_cannot_access_finance_resources(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $account = Account::factory()->for($owner)->create();
        $token = $other->createToken('test', ['accounts:read'])->plainTextToken;

        $this->asApiToken($token)
            ->getJson(route('api.v1.accounts.show', $account))
            ->assertForbidden();
    }

    private function asApiToken(string $token): static
    {
        app('auth')->forgetGuards();

        return $this->withToken($token)->withHeader('Accept', 'application/json');
    }
}
