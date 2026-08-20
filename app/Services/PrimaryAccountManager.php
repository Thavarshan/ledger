<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;

/**
 * Maintains the one-primary-account invariant for an owner.
 */
final class PrimaryAccountManager
{
    /**
     * Clear all primary flags except an optional account.
     *
     * The owner row is locked first so the invariant remains safe even when
     * the owner has no accounts yet and concurrent writes are possible.
     */
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
