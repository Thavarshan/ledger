<?php

namespace App\Actions;

use App\Models\User;

/**
 * Permanently removes a user and cascading owned records.
 */
final class DeleteUser
{
    public function handle(User $user): void
    {
        $user->delete();
    }
}
