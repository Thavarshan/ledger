<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;

/**
 * Resolves an active account belonging to a user.
 */
final class ActiveOwnedAccount
{
    public function find(User $owner, int $accountId): Account
    {
        return $owner->accounts()->active()->findOrFail($accountId);
    }
}
