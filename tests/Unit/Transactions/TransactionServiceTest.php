<?php

namespace Tests\Unit\Transactions;

use App\Actions\CreateTransaction;
use App\Actions\UpdateTransaction;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_create_persists_a_transaction_on_an_active_owned_account(): void
    {
        $account = Account::factory()->create();
        $transaction = app(CreateTransaction::class)->handle($account->user, [
            'account_id' => $account->id,
            'direction' => 'credit',
            'amount_minor' => '2500',
            'description' => 'Salary',
            'occurred_at' => '2026-08-01T08:00:00Z',
        ]);

        $this->assertModelExists($transaction);
        $this->assertSame($account->id, $transaction->account_id);
        $this->assertSame(2500, $transaction->amount_minor);
    }

    public function test_create_rejects_an_inactive_account(): void
    {
        $account = Account::factory()->inactive()->create();

        $this->expectException(ModelNotFoundException::class);
        app(CreateTransaction::class)->handle($account->user, [
            'account_id' => $account->id,
            'direction' => 'debit',
            'amount_minor' => 100,
            'description' => 'Blocked',
            'occurred_at' => '2026-08-01T08:00:00Z',
        ]);
    }

    public function test_update_can_move_a_transaction_to_another_active_account(): void
    {
        $user = User::factory()->create();
        $source = Account::factory()->for($user)->create();
        $target = Account::factory()->for($user)->create();
        $transaction = Transaction::factory()->forAccount($source)->create();

        $updated = app(UpdateTransaction::class)->handle($user, $transaction, [
            'account_id' => $target->id,
            'description' => 'Moved transaction',
        ]);

        $this->assertSame($target->id, $updated->account_id);
        $this->assertSame('Moved transaction', $updated->description);
    }

    public function test_update_can_keep_a_transaction_on_its_inactive_account(): void
    {
        $account = Account::factory()->inactive()->create();
        $transaction = Transaction::factory()->forAccount($account)->create();

        $updated = app(UpdateTransaction::class)->handle($account->user, $transaction, [
            'account_id' => $account->id,
            'description' => 'Corrected historical transaction',
        ]);

        $this->assertSame($account->id, $updated->account_id);
        $this->assertSame('Corrected historical transaction', $updated->description);
    }

    public function test_update_rejects_a_transaction_owned_by_another_user(): void
    {
        $account = Account::factory()->create();
        $transaction = Transaction::factory()->forAccount($account)->create();

        $this->expectException(ModelNotFoundException::class);
        app(UpdateTransaction::class)->handle(User::factory()->create(), $transaction, [
            'description' => 'Blocked',
        ]);
    }
}
