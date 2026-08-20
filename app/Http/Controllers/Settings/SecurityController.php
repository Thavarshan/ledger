<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use App\Http\Requests\Settings\TwoFactorAuthenticationRequest;
use App\Models\User;
use App\Services\SecuritySettingsProps;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

/**
 * Handles password and security-settings interactions for the web client.
 *
 * Fortify remains the source of truth for feature availability and security
 * state validation; this controller only assembles the page and redirects.
 */
class SecurityController extends Controller
{
    /**
     * Show the user's security settings page.
     */
    public function edit(TwoFactorAuthenticationRequest $request, SecuritySettingsProps $props): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(401);
        }

        if (Features::canManageTwoFactorAuthentication()) {
            $request->ensureStateIsValid();
        }

        return Inertia::render('settings/security', $props->for($user));
    }

    /**
     * Update the user's password.
     */
    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(401);
        }

        $user->update(['password' => $request->string('password')->toString()]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Password updated.')]);

        return back();
    }
}
