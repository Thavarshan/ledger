<?php

namespace App\Actions;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

/**
 * Updates a transaction and safely supports account reassignment.
 */
final class UpdateTransaction
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Transaction $transaction, array $attributes): Transaction
    {
        return DB::transaction(function () use ($transaction, $attributes): Transaction {
            $ownerId = $transaction->account()->value('user_id');

            if (array_key_exists('account_id', $attributes) && (int) $attributes['account_id'] !== $transaction->account_id) {
                Account::query()
                    ->active()
                    ->where('user_id', $ownerId)
                    ->findOrFail((int) $attributes['account_id']);
            }

            $transaction->update($attributes);

            return $transaction->refresh()->load('account:id,name,currency_code');
        });
    }
}
