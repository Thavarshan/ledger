<?php

namespace App\Actions;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Updates a transaction while preserving account ownership boundaries.
 *
 * Reassignment is allowed only to another active account belonging to the
 * transaction's original owner, preventing ownership changes through input.
 */
final class UpdateTransaction
{
    /**
     * Update a transaction while safely validating account reassignment.
     *
     * The transaction and candidate accounts are locked before validation so
     * reassignment and account deactivation cannot interleave inconsistently.
     *
     * @param  Transaction  $transaction  The transaction being changed.
     * @param  array<string, mixed>  $attributes
     * @return Transaction The refreshed transaction with its account loaded.
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
