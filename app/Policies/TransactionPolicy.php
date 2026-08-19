<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

/**
 * Authorizes transactions through their owning account.
 */
class TransactionPolicy
{
    /**
     * Determine whether the user can view any transactions.
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
     * Determine whether the user can create transactions.
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
