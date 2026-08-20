<?php

namespace Tests\Feature\Accounts;

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_from_every_account_route(): void
    {
        $account = Account::factory()->create();
        $routes = [
            ['get', route('accounts.index')],
            ['get', route('accounts.create')],
            ['post', route('accounts.store')],
            ['get', route('accounts.show', $account)],
            ['get', route('accounts.edit', $account)],
            ['put', route('accounts.update', $account)],
            ['delete', route('accounts.destroy', $account)],
        ];

        foreach ($routes as [$method, $url]) {
            $this->{$method}($url)->assertRedirect(route('login'));
        }
    }

    public function test_owner_can_view_the_index_with_pagination_and_query_string(): void
    {
        $user = User::factory()->create();
        Account::factory()->for($user)->count(13)->create();
        Account::factory()->count(2)->create();

        $response = $this->actingAs($user)
            ->withHeaders($this->inertiaHeaders())
            ->get(route('accounts.index', ['search' => 'bank', 'page' => 1]));

        $response->assertOk()
            ->assertJsonPath('component', 'accounts/index')
            ->assertJsonCount(12, 'props.accounts.data')
            ->assertJsonPath('props.accounts.meta.per_page', 12)
            ->assertJsonPath('props.accounts.meta.path', route('accounts.index'));
    }

    public function test_owner_can_view_the_create_form(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->withHeaders($this->inertiaHeaders())
            ->get(route('accounts.create'));

        $response->assertOk()
            ->assertJsonPath('component', 'accounts/create')
            ->assertJsonPath('props.accountTypes.0', 'savings')
            ->assertJsonPath('props.currencies.0', 'LKR');
    }

    public function test_account_pages_include_the_derived_balance_in_minor_units(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        Transaction::factory()->forAccount($account)->credit()->create(['amount_minor' => 12500]);
        Transaction::factory()->forAccount($account)->debit()->create(['amount_minor' => 3250]);

        $this->actingAs($user)
            ->withHeaders($this->inertiaHeaders())
            ->get(route('accounts.index'))
            ->assertOk()
            ->assertJsonPath('props.accounts.data.0.balance_minor', '9250');

        $this->actingAs($user)
            ->withHeaders($this->inertiaHeaders())
            ->get(route('accounts.show', $account))
            ->assertOk()
            ->assertJsonPath('props.account.data.balance_minor', '9250');
    }

    public function test_owner_can_filter_search_and_sort_accounts(): void
    {
        $user = User::factory()->create();
        $match = Account::factory()->for($user)->create([
            'name' => 'Alpha operating account',
            'account_type' => 'current',
            'country_code' => 'US',
            'currency_code' => 'USD',
            'bank_name' => 'International Bank',
        ]);
        Account::factory()->for($user)->create(['name' => 'Zulu account']);
        Account::factory()->create(['name' => 'Alpha other user']);

        $response = $this->actingAs($user)
            ->withHeaders($this->inertiaHeaders())
            ->get(route('accounts.index', [
                'search' => 'international',
                'account_type' => 'current',
                'country_code' => 'us',
                'currency_code' => 'usd',
                'sort' => 'NAME:ASC',
            ]));

        $response->assertOk()
            ->assertJsonCount(1, 'props.accounts.data')
            ->assertJsonPath('props.accounts.data.0.id', $match->id);
    }

    public function test_invalid_index_parameters_are_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('accounts.index', [
            'sort' => 'password:asc',
        ]))->assertSessionHasErrors('sort');

        $this->actingAs($user)->get(route('accounts.index', [
            'currency_code' => 'XXX',
        ]))->assertSessionHasErrors('currency_code');
    }

    public function test_owner_can_create_an_account_with_normalized_codes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('accounts.store'), $this->accountData([
            'country_code' => 'lk',
            'currency_code' => 'lkr',
            'account_number' => '001234567890',
            'account_number_last4' => '0000',
        ]));

        $account = Account::query()->whereBelongsTo($user)->firstOrFail();

        $response->assertRedirect(route('accounts.show', $account));
        $this->assertSame('LK', $account->country_code);
        $this->assertSame('LKR', $account->currency_code->value);
        $this->assertSame('7890', $account->account_number_last4);
        $this->assertSame('001234567890', $account->account_number);
    }

    public function test_create_validation_reports_invalid_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('accounts.store'), [
            'name' => str_repeat('x', 256),
            'account_type' => 'invalid',
            'bank_name' => str_repeat('x', 151),
            'country_code' => 'L',
            'currency_code' => 'XXX',
            'account_number' => '',
            'swift_bic' => 'invalid',
        ]);

        $response->assertSessionHasErrors([
            'name', 'account_type', 'bank_name', 'country_code',
            'currency_code', 'account_number', 'swift_bic',
        ]);
    }

    public function test_owner_can_view_show_and_edit_pages_without_raw_identifiers(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->foreign()->create([
            'iban' => 'GB82WEST12345698765432',
            'routing_number' => '123456789',
            'sort_code' => '123456',
        ]);

        foreach ([route('accounts.show', $account), route('accounts.edit', $account)] as $url) {
            $response = $this->actingAs($user)
                ->withHeaders($this->inertiaHeaders())
                ->get($url);

            $response->assertOk()
                ->assertJsonPath('props.account.data.id', $account->id)
                ->assertJsonMissingPath('props.account.data.iban')
                ->assertJsonMissingPath('props.account.data.routing_number')
                ->assertJsonMissingPath('props.account.data.sort_code');
        }
    }

    public function test_non_owner_cannot_view_update_or_delete_an_account(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $account = Account::factory()->for($owner)->create();

        $this->actingAs($user)->get(route('accounts.show', $account))->assertForbidden();
        $this->actingAs($user)->get(route('accounts.edit', $account))->assertForbidden();
        $this->actingAs($user)->put(route('accounts.update', $account), [])->assertForbidden();
        $this->actingAs($user)->delete(route('accounts.destroy', $account))->assertForbidden();
    }

    public function test_owner_can_update_an_account_and_replace_its_number(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create(['account_number' => '111122223333']);

        $response = $this->actingAs($user)->put(route('accounts.update', $account), [
            'name' => 'Updated account',
            'country_code' => 'gb',
            'currency_code' => 'gbp',
            'account_number' => '999988887777',
        ]);

        $account->refresh();
        $response->assertRedirect(route('accounts.show', $account));
        $this->assertSame('Updated account', $account->name);
        $this->assertSame('GB', $account->country_code);
        $this->assertSame('GBP', $account->currency_code->value);
        $this->assertSame('7777', $account->account_number_last4);
        $this->assertSame('999988887777', $account->account_number);
    }

    public function test_nullable_account_fields_can_be_cleared(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->foreign()->create([
            'iban' => 'GB82WEST12345698765432',
            'swift_bic' => 'BCEYLKLX',
        ]);

        $response = $this->actingAs($user)->put(route('accounts.update', $account), [
            'iban' => '',
            'swift_bic' => '',
        ]);

        $response->assertRedirect(route('accounts.show', $account));
        $account->refresh();
        $this->assertNull($account->iban);
        $this->assertNull($account->swift_bic);
    }

    public function test_owner_can_delete_an_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('accounts.destroy', $account));

        $response->assertRedirect(route('accounts.index'));
        $this->assertModelMissing($account);
    }

    /** @param array<string, mixed> $overrides */
    private function accountData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Primary account',
            'account_type' => 'savings',
            'account_holder_name' => 'Test User',
            'bank_name' => 'Bank of Ceylon',
            'bank_code' => '7010',
            'branch_name' => 'Colombo Branch',
            'branch_code' => '001',
            'country_code' => 'LK',
            'currency_code' => 'LKR',
            'account_number' => '001234567890',
            'swift_bic' => 'BCEYLKLX',
            'is_primary' => false,
            'is_active' => true,
        ], $overrides);
    }

    /** @return array<string, string> */
    private function inertiaHeaders(): array
    {
        return [
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
            'X-Inertia-Version' => app(HandleInertiaRequests::class)->version(Request::create('/')),
        ];
    }
}
