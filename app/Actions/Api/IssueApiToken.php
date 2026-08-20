<?php

namespace App\Actions\Api;

use App\Exceptions\ApiInvalidCredentials;
use App\Exceptions\ApiInvalidTwoFactorCode;
use App\Exceptions\ApiTwoFactorRequired;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;

final class IssueApiToken
{
    public function __construct(private readonly TwoFactorAuthenticationProvider $twoFactorProvider) {}

    /**
     * @param  array<string, mixed>  $credentials
     * @return array{token: string, expires_at: CarbonImmutable, user: User}
     */
    public function handle(array $credentials): array
    {
        $user = $this->authenticate($credentials);

        return DB::transaction(function () use ($credentials, $user): array {
            $this->verifySecondFactor($user, $credentials);

            $configuredTtl = config('api.mobile_token_ttl_days', 30);
            $ttl = is_int($configuredTtl)
                ? $configuredTtl
                : (is_string($configuredTtl) && ctype_digit($configuredTtl) ? (int) $configuredTtl : 30);
            $expiresAt = now()->addDays($ttl);
            $deviceName = is_string($credentials['device_name'] ?? null) ? $credentials['device_name'] : 'mobile';
            $token = $user->createToken(
                'mobile:'.$deviceName,
                [
                    'accounts:read', 'accounts:write',
                    'transactions:read', 'transactions:write',
                    'profile:read', 'profile:write', 'tokens:manage',
                ],
                $expiresAt,
            );

            return [
                'token' => $token->plainTextToken,
                'expires_at' => $expiresAt,
                'user' => $user,
            ];
        });
    }

    /** @param array<string, mixed> $credentials */
    private function authenticate(array $credentials): User
    {
        $provider = Auth::guard('web')->getProvider();
        $user = $provider->retrieveByCredentials([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ]);

        if (! $user instanceof Authenticatable || ! $provider->validateCredentials($user, $credentials)) {
            throw new ApiInvalidCredentials('The provided credentials are invalid.');
        }

        if (! $user instanceof User) {
            throw new ApiInvalidCredentials('The provided credentials are invalid.');
        }

        return $user;
    }

    /** @param array<string, mixed> $credentials */
    private function verifySecondFactor(User $user, array $credentials): void
    {
        if (! $user->hasEnabledTwoFactorAuthentication()) {
            return;
        }

        $code = is_string($credentials['code'] ?? null) ? $credentials['code'] : null;
        $recoveryCode = is_string($credentials['recovery_code'] ?? null) ? $credentials['recovery_code'] : null;

        if ($code === null && $recoveryCode === null) {
            throw new ApiTwoFactorRequired('Two-factor authentication is required.');
        }

        if ($recoveryCode !== null) {
            if (! in_array($recoveryCode, $user->recoveryCodes(), true)) {
                throw new ApiInvalidTwoFactorCode('The two-factor code is invalid.');
            }

            $user->replaceRecoveryCode($recoveryCode);

            return;
        }

        $encryptedSecret = $user->two_factor_secret;

        if (! is_string($encryptedSecret)) {
            throw new ApiInvalidTwoFactorCode('The two-factor code is invalid.');
        }

        $secret = Fortify::currentEncrypter()->decrypt($encryptedSecret);

        if (! is_string($secret) || ! $this->twoFactorProvider->verify($secret, (string) $code)) {
            throw new ApiInvalidTwoFactorCode('The two-factor code is invalid.');
        }
    }
}
