<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;

/**
 * Maintains the one-primary-account invariant for an owner.
 */
final class PrimaryAccountManager
{
    public function clearFor(User $owner, ?Account $except = null): void
    {
        $accounts = Account::query()->whereBelongsTo($owner)->lockForUpdate();

        $accounts->get(['id']);

        $accounts
            ->where('is_primary', true)
            ->when($except, fn ($query) => $query->whereKeyNot($except->getKey()))
            ->update(['is_primary' => false]);
    }
}
