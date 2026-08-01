<?php

namespace App\Http\Controllers\Settings;

use App\Actions\UpdatePassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use App\Http\Requests\Settings\TwoFactorAuthenticationRequest;
use App\Services\SecuritySettingsProps;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class SecurityController extends Controller
{
    /**
     * Show the user's security settings page.
     */
    public function edit(TwoFactorAuthenticationRequest $request, SecuritySettingsProps $props): Response
    {
        if (Features::canManageTwoFactorAuthentication()) {
            $request->ensureStateIsValid();
        }

        return Inertia::render('settings/security', $props->for($request->user()));
    }

    /**
     * Update the user's password.
     */
    public function update(PasswordUpdateRequest $request, UpdatePassword $update): RedirectResponse
    {
        $update->handle($request->user(), $request->string('password')->toString());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Password updated.')]);

        return back();
    }
}
