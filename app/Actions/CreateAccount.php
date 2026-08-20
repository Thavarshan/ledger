<?php

namespace App\Actions;

use App\Models\Account;
use App\Models\User;
use App\Services\AccountAttributePreparer;
use App\Services\PrimaryAccountManager;
use Illuminate\Support\Facades\DB;

/**
 * Persists a new account for an authenticated owner.
 *
 * The action centralizes account attribute preparation and the primary-account
 * invariant so web and API callers cannot create conflicting account state.
 */
final class CreateAccount
{
    public function __construct(
        /**
         * Removes client-controlled fields and derives safe account metadata.
         */
        private readonly AccountAttributePreparer $attributes,
        /**
         * Coordinates locking and clearing of an owner's primary account flag.
         */
        private readonly PrimaryAccountManager $primaryAccounts,
    ) {}

    /**
     * Create an account and maintain the owner's primary-account invariant.
     *
     * A requested primary account clears the owner's previous primary account
     * in the same transaction before the new row is inserted.
     *
     * @param  User  $owner  The user who will own the new account.
     * @param  array<string, mixed>  $attributes
     * @return Account The newly persisted account.
     */
    public function handle(User $owner, array $attributes): Account
    {
        $attributes = $this->attributes->prepare($attributes);

        return DB::transaction(function () use ($owner, $attributes): Account {
            if (($attributes['is_primary'] ?? false) === true) {
                $this->primaryAccounts->clearFor($owner);
            }

            return $owner->accounts()->create($attributes);
        });
    }
}
