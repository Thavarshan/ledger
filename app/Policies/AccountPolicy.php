<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;

/**
 * Authorizes account operations using the account's owning user.
 *
 * Collection visibility and creation are handled by authenticated routes;
 * individual account operations still require an exact owner match.
 */
class AccountPolicy
{
    /**
     * Determine whether the authenticated user may request an account listing.
     *
     * The index query applies the owner's scope, so the policy only needs to
     * confirm that an authenticated actor may enter the operation.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Account $account): bool
    {
        return $user->id === $account->user_id;
    }

    /**
     * Determine whether the authenticated user may create an account.
     *
     * Attribute validation and primary-account rules are enforced elsewhere.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Account $account): bool
    {
        return $user->id === $account->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Account $account): bool
    {
        return $user->id === $account->user_id;
    }
}
