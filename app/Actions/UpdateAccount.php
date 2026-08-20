<?php

namespace App\Actions;

use App\Models\Account;
use App\Services\AccountAttributePreparer;
use App\Services\PrimaryAccountManager;
use Illuminate\Support\Facades\DB;

/**
 * Updates an existing account while preserving owner-level invariants.
 *
 * The account itself supplies the owner identity; callers cannot provide a
 * different owner while the action clears competing primary accounts.
 */
final class UpdateAccount
{
    public function __construct(
        /**
         * Normalizes writable account attributes before persistence.
         */
        private readonly AccountAttributePreparer $attributes,
        /**
         * Enforces the single-primary-account rule for the account owner.
         */
        private readonly PrimaryAccountManager $primaryAccounts,
    ) {}

    /**
     * Update an account while preserving the primary-account invariant.
     *
     * When the account is marked primary, the current owner is resolved from
     * the model before other primary flags are cleared in the same transaction.
     *
     * @param  Account  $account  The owned account being changed.
     * @param  array<string, mixed>  $attributes
     * @return Account The refreshed account after persistence.
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
