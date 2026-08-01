<?php

namespace Tests\Unit\Transactions;

use App\Enums\CurrencyCode;
use App\Enums\TransactionDirection;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TransactionResourceTest extends TestCase
{
    public function test_it_serializes_the_safe_transaction_shape_and_account_summary(): void
    {
        $account = new Account;
        $account->forceFill([
            'id' => 7,
            'name' => 'Operating account',
            'currency_code' => CurrencyCode::LKR,
        ]);

        $transaction = new Transaction;
        $transaction->forceFill([
            'id' => 11,
            'account_id' => 7,
            'direction' => TransactionDirection::DEBIT,
            'amount_minor' => 125000,
            'description' => 'Office supplies',
            'reference' => 'TXN-000011',
            'notes' => 'Monthly purchase',
            'occurred_at' => Carbon::parse('2026-08-01T10:15:00Z'),
        ]);
        $transaction->setRelation('account', $account);
        $data = (new TransactionResource($transaction))->toArray(Request::create('/'));

        $this->assertSame('debit', $data['direction']);
        $this->assertSame('125000', $data['amount_minor']);
        $this->assertSame('2026-08-01T10:15:00.000000Z', $data['occurred_at']);
        $this->assertSame([
            'id', 'account_id', 'direction', 'amount_minor', 'description',
            'reference', 'notes', 'occurred_at', 'account', 'created_at', 'updated_at',
        ], array_keys($data));
        $this->assertSame(['id' => 7, 'name' => 'Operating account', 'currency_code' => 'LKR'], $data['account']);
    }

    public function test_it_omits_an_unloaded_account(): void
    {
        $transaction = new Transaction;
        $transaction->forceFill([
            'direction' => TransactionDirection::CREDIT,
            'amount_minor' => 100,
            'occurred_at' => Carbon::now(),
        ]);

        $data = (new TransactionResource($transaction))->toArray(Request::create('/'));

        $this->assertInstanceOf(MissingValue::class, $data['account']);
    }
}
