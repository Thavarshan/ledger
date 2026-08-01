<?php

namespace Tests\Unit\Transactions;

use App\Enums\TransactionDirection;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TransactionModelTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_casts_direction_amount_and_occurred_at(): void
    {
        $casts = (new Transaction)->getCasts();

        $this->assertSame(TransactionDirection::class, $casts['direction']);
        $this->assertSame('integer', $casts['amount_minor']);
        $this->assertSame('immutable_datetime', $casts['occurred_at']);
    }

    public function test_it_belongs_to_an_account_and_exposes_user_transaction_relations(): void
    {
        $account = Account::factory()->create();
        $transaction = Transaction::factory()->forAccount($account)->create();
        $user = $account->user;

        $this->assertSame('account_id', $transaction->account()->getForeignKeyName());
        $this->assertTrue($user->transactions()->whereKey($transaction->id)->exists());
        $this->assertSame(['description', 'reference'], $this->invoke($transaction, 'searchableColumns'));
        $this->assertSame([
            'occurred_at' => 'occurred_at',
            'amount_minor' => 'amount_minor',
            'description' => 'description',
            'created_at' => 'created_at',
        ], $this->invoke($transaction, 'sortableColumns'));
    }

    private function invoke(Transaction $transaction, string $method): mixed
    {
        $reflection = new \ReflectionMethod($transaction, $method);
        $reflection->setAccessible(true);

        return $reflection->invoke($transaction);
    }
}
