<?php

namespace App\Actions;

use App\Models\User;

/**
 * Updates a user's password through the model's hash cast.
 */
final class UpdatePassword
{
    public function handle(User $user, string $password): void
    {
        $user->update(['password' => $password]);
    }
}
