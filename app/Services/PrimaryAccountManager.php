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
        $exceptId = $except?->getKey();

        $owner->newQuery()->whereKey($owner->getKey())->lockForUpdate()->firstOrFail();

        Account::query()
            ->whereBelongsTo($owner)
            ->where('is_primary', true)
            ->when($exceptId !== null, fn ($query) => $query->whereKeyNot($exceptId))
            ->update(['is_primary' => false]);
    }
}
