<?php

namespace App\Actions;

use App\Models\Account;
use App\Models\User;
use App\Services\AccountAttributePreparer;
use App\Services\PrimaryAccountManager;
use Illuminate\Support\Facades\DB;

/**
 * Creates an account while preserving account-owner invariants.
 */
final class CreateAccount
{
    public function __construct(
        private readonly AccountAttributePreparer $attributes,
        private readonly PrimaryAccountManager $primaryAccounts,
    ) {}

    /**
     * Create an account and maintain the owner's primary-account invariant.
     *
     * @param  array<string, mixed>  $attributes
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
