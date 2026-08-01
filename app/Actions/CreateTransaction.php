<?php

namespace App\Actions;

use App\Models\Transaction;
use App\Models\User;
use App\Services\ActiveOwnedAccount;
use Illuminate\Support\Facades\DB;

/**
 * Creates a transaction on an active account owned by the user.
 */
final class CreateTransaction
{
    public function __construct(private readonly ActiveOwnedAccount $accounts) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(User $owner, array $attributes): Transaction
    {
        return DB::transaction(function () use ($owner, $attributes): Transaction {
            $account = $this->accounts->find($owner, (int) $attributes['account_id']);

            return $account->transactions()->create($attributes)->load('account:id,name,currency_code');
        });
    }
}
