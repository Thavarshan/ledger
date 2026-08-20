<?php

namespace App\Actions;

use App\Models\Account;
use App\Services\AccountAttributePreparer;
use App\Services\PrimaryAccountManager;
use Illuminate\Support\Facades\DB;

/**
 * Updates an account while preserving account-owner invariants.
 */
final class UpdateAccount
{
    public function __construct(
        private readonly AccountAttributePreparer $attributes,
        private readonly PrimaryAccountManager $primaryAccounts,
    ) {}

    /**
     * Update an account while preserving the primary-account invariant.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Account $account, array $attributes): Account
    {
        $attributes = $this->attributes->prepare($attributes);

        return DB::transaction(function () use ($account, $attributes): Account {
            if (($attributes['is_primary'] ?? false) === true) {
                $this->primaryAccounts->clearFor($account->user()->firstOrFail(), $account);
            }

            $account->update($attributes);

            return $account->refresh();
        });
    }
}
