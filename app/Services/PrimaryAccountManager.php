<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;

/**
 * Maintains the one-primary-account invariant for an owner.
 *
 * The owner row is locked before account flags are updated, giving all account
 * creation and update actions one small, reusable concurrency boundary.
 */
final class PrimaryAccountManager
{
    /**
     * Clear all primary flags except an optional account.
     *
     * The owner row is locked first so the invariant remains safe even when
     * the owner has no accounts yet and concurrent writes are possible.
     *
     * @param  User  $owner  The owner whose account flags should be normalized.
     * @param  Account|null  $except  An account that may remain primary.
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
