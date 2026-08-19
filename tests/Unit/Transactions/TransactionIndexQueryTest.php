<?php

namespace Tests\Unit\Transactions;

use App\Enums\TransactionDirection;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\TransactionIndexQuery;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TransactionIndexQueryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_transaction_criteria_apply_search_direction_account_and_date_range(): void
    {
        $account = Account::factory()->create();
        $match = Transaction::factory()->forAccount($account)->credit()->create([
            'description' => 'Salary payment',
            'occurred_at' => '2026-08-01 10:00:00',
        ]);
        Transaction::factory()->forAccount($account)->debit()->create([
            'description' => 'Rent payment',
            'occurred_at' => '2026-08-01 12:00:00',
        ]);

        $transactions = app(TransactionIndexQuery::class)->paginate($account->user, [
            'account_id' => $account->id,
            'direction' => TransactionDirection::CREDIT->value,
            'search' => 'salary',
            'occurred_from' => '2026-08-01',
            'occurred_to' => '2026-08-01',
            'sort' => 'occurred_at:asc',
        ]);

        $this->assertSame([$match->id], $transactions->getCollection()->modelKeys());
    }

    public function test_transactions_are_scoped_to_the_owner(): void
    {
        $account = Account::factory()->create();
        $ownedTransaction = Transaction::factory()->forAccount($account)->create();
        $otherTransaction = Transaction::factory()->create();

        $transactions = app(TransactionIndexQuery::class)->paginate($account->user, []);

        $this->assertContains($ownedTransaction->id, $transactions->getCollection()->modelKeys());
        $this->assertNotContains($otherTransaction->id, $transactions->getCollection()->modelKeys());
    }
}
