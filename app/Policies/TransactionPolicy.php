<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

/**
 * Authorizes transaction operations through their owning account.
 *
 * Transactions do not store a user ID directly, so ownership is resolved via
 * the account relation for every model-level authorization decision.
 */
class TransactionPolicy
{
    /**
     * Determine whether the authenticated user may request a transaction list.
     *
     * The index query applies the actual owner scope before returning records.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view a transaction.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $this->owns($user, $transaction);
    }

    /**
     * Determine whether the authenticated user may create a transaction.
     *
     * The create action performs the active-account ownership check.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update a transaction.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return $this->owns($user, $transaction);
    }

    /**
     * Determine whether the user can delete a transaction.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $this->owns($user, $transaction);
    }

    /**
     * Determine whether a transaction belongs to the user.
     */
    private function owns(User $user, Transaction $transaction): bool
    {
        return $transaction->account()->whereBelongsTo($user)->exists();
    }
}
