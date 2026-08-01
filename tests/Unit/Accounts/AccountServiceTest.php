<?php

namespace Tests\Unit\Accounts;

use App\Actions\CreateAccount;
use App\Actions\UpdateAccount;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AccountServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_create_derives_last_four_and_ignores_client_metadata(): void
    {
        $user = User::factory()->create();
        $account = app(CreateAccount::class)->handle($user, Account::factory()->raw([
            'account_number' => '001234567890',
            'account_number_last4' => '9999',
        ]));

        $this->assertSame('7890', $account->account_number_last4);
        $this->assertSame('001234567890', $account->account_number);
    }

    public function test_promoting_an_account_clears_the_existing_primary(): void
    {
        $user = User::factory()->create();
        $existing = Account::factory()->for($user)->create(['is_primary' => true]);
        $account = Account::factory()->for($user)->create();

        app(UpdateAccount::class)->handle($user, $account, ['is_primary' => true]);

        $this->assertFalse((bool) $existing->refresh()->is_primary);
        $this->assertTrue((bool) $account->refresh()->is_primary);
    }

    public function test_update_preserves_the_existing_account_number_when_it_is_omitted(): void
    {
        $account = Account::factory()->create(['account_number' => '001234567890']);

        app(UpdateAccount::class)->handle($account->user, $account, ['is_active' => false]);

        $this->assertSame('001234567890', $account->refresh()->account_number);
        $this->assertFalse((bool) $account->is_active);
    }

    public function test_failed_create_rolls_back_primary_reassignment(): void
    {
        $user = User::factory()->create();
        $existing = Account::factory()->for($user)->create(['is_primary' => true]);

        $this->expectException(QueryException::class);
        try {
            app(CreateAccount::class)->handle($user, Account::factory()->raw([
                'bank_name' => null,
                'is_primary' => true,
            ]));
        } finally {
            $this->assertTrue((bool) $existing->refresh()->is_primary);
        }
    }
}
