<?php

namespace App\Actions;

use App\Models\User;

/**
 * Updates a user's profile and resets verification after an email change.
 */
final class UpdateProfile
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(User $user, array $attributes): void
    {
        $user->fill($attributes);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
    }
}
