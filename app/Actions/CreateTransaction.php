<?php

namespace App\Actions;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Persists a transaction against an active account owned by the user.
 *
 * Account lookup is performed inside a transaction with a row lock so a
 * concurrent account deactivation cannot race the mutation.
 */
final class CreateTransaction
{
    /**
     * Create a transaction after locking and validating its active account.
     *
     * The account relation is loaded before returning so every caller receives
     * the same safe nested account data in the response resource.
     *
     * @param  User  $owner  The authenticated owner of the target account.
     * @param  array<string, mixed>  $attributes
     * @return Transaction The newly persisted transaction with its account loaded.
     */
    public function handle(User $owner, array $attributes): Transaction
    {
        return DB::transaction(function () use ($owner, $attributes): Transaction {
            $accountId = $attributes['account_id'] ?? null;

            if (! is_int($accountId) && ! (is_string($accountId) && ctype_digit($accountId))) {
                throw new InvalidArgumentException('A valid account ID is required.');
            }

            $account = $owner->accounts()
                ->active()
                ->whereKey((int) $accountId)
                ->lockForUpdate()
                ->firstOrFail();

            return $account->transactions()->create($attributes)->load('account:id,name,currency_code');
        });
    }
}
