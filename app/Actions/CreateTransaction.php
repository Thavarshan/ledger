<?php

namespace App\Actions;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Creates a transaction on an active account owned by the user.
 */
final class CreateTransaction
{
    /**
     * @param  array<string, mixed>  $attributes
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
