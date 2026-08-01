<?php

namespace App\Actions;

use App\Models\Account;
use App\Models\User;
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
     * @param  array<string, mixed>  $attributes
     */
    public function handle(User $owner, Account $account, array $attributes): Account
    {
        $attributes = $this->attributes->prepare($attributes);

        return DB::transaction(function () use ($owner, $account, $attributes): Account {
            if (($attributes['is_primary'] ?? false) === true) {
                $this->primaryAccounts->clearFor($owner, $account);
            }

            $account->update($attributes);

            return $account->refresh();
        });
    }
}
