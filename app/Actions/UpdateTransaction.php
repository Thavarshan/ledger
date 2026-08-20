<?php

namespace App\Actions;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

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
            $transaction = Transaction::query()
                ->whereKey($transaction->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $targetAccountId = $attributes['account_id'] ?? null;

            if ($targetAccountId !== null
                && ! is_int($targetAccountId)
                && ! (is_string($targetAccountId) && ctype_digit($targetAccountId))) {
                throw new InvalidArgumentException('A valid account ID is required.');
            }

            $accountIds = collect([$transaction->account_id, $targetAccountId])
                ->filter()
                ->map(fn (mixed $id): int => (int) $id)
                ->unique()
                ->sort()
                ->values();
            $accounts = Account::query()
                ->whereKey($accountIds->all())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $ownerId = $accounts->get($transaction->account_id)?->user_id;

            if ($ownerId === null) {
                throw (new ModelNotFoundException)->setModel(Account::class, [$transaction->account_id]);
            }

            if ($targetAccountId !== null && (int) $targetAccountId !== $transaction->account_id) {
                $targetAccount = $accounts->get((int) $targetAccountId);

                if ($targetAccount?->is_active !== true || $targetAccount->user_id !== $ownerId) {
                    throw (new ModelNotFoundException)->setModel(Account::class, [(int) $targetAccountId]);
                }
            }

            $transaction->update($attributes);

            return $transaction->refresh()->load('account:id,name,currency_code');
        });
    }
}
