<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Features;

/**
 * Builds the security settings presentation contract.
 */
final class SecuritySettingsProps
{
    /**
     * Build the security settings presentation payload for a user.
     *
     * @return array<string, mixed>
     */
    public function for(User $user): array
    {
        $canManagePasskeys = Features::canManagePasskeys();
        $canManageTwoFactor = Features::canManageTwoFactorAuthentication();

        $props = [
            'canManageTwoFactor' => $canManageTwoFactor,
            'canManagePasskeys' => $canManagePasskeys,
            'passkeys' => $canManagePasskeys
                ? $user->passkeys()
                    ->select(['id', 'name', 'credential', 'created_at', 'last_used_at'])
                    ->latest()
                    ->get()
                    ->map(fn ($passkey): array => [
                        'id' => $passkey->id,
                        'name' => $passkey->name,
                        'authenticator' => $passkey->authenticator,
                        'created_at_diff' => $passkey->created_at?->diffForHumans(),
                        'last_used_at_diff' => $passkey->last_used_at?->diffForHumans(),
                    ])
                    ->values()
                    ->all()
                : [],
            'passwordRules' => Password::defaults()->toPasswordRulesString(),
        ];

        if ($canManageTwoFactor) {
            $props['twoFactorEnabled'] = $user->hasEnabledTwoFactorAuthentication();
            $props['requiresConfirmation'] = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
        }

        return $props;
    }
}
