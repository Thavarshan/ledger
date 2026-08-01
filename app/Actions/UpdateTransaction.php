<?php

namespace App\Actions;

use App\Models\Transaction;
use App\Models\User;
use App\Services\ActiveOwnedAccount;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * Updates a transaction and safely supports account reassignment.
 */
final class UpdateTransaction
{
    public function __construct(private readonly ActiveOwnedAccount $accounts) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(User $owner, Transaction $transaction, array $attributes): Transaction
    {
        return DB::transaction(function () use ($owner, $transaction, $attributes): Transaction {
            if (! $transaction->account()->whereBelongsTo($owner)->exists()) {
                throw (new ModelNotFoundException)->setModel(Transaction::class, [$transaction->getKey()]);
            }

            if (array_key_exists('account_id', $attributes) && (int) $attributes['account_id'] !== $transaction->account_id) {
                $this->accounts->find($owner, (int) $attributes['account_id']);
            }

            $transaction->update($attributes);

            return $transaction->refresh()->load('account:id,name,currency_code');
        });
    }
}
