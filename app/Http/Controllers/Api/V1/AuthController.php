<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Api\IssueApiToken;
use App\Actions\Fortify\ResetUserPassword;
use App\Exceptions\ApiInvalidCredentials;
use App\Exceptions\ApiInvalidTwoFactorCode;
use App\Exceptions\ApiTwoFactorRequired;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\ResetPasswordRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

/**
 * Handles bearer-token authentication and password recovery for API v1.
 *
 * Session-based Fortify authentication remains separate from this controller.
 */
class AuthController extends Controller
{
    /**
     * Issue a mobile bearer token for an existing user.
     *
     * Users with enabled two-factor authentication must provide a valid TOTP
     * or one-time recovery code before all mobile abilities are granted.
     */
    public function login(LoginRequest $request, IssueApiToken $issue): JsonResponse
    {
        try {
            $result = $issue->handle($request->validated());
        } catch (ApiTwoFactorRequired $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'code' => 'two_factor_required',
            ], 409);
        } catch (ApiInvalidTwoFactorCode|ApiInvalidCredentials $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'data' => [
                'token' => $result['token'],
                'token_type' => 'Bearer',
                'expires_at' => $result['expires_at']->toISOString(),
                'user' => UserResource::make($result['user'])->resolve($request),
            ],
        ]);
    }

    /**
     * Send a generic password-reset response without revealing account existence.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        Password::sendResetLink($request->validated());

        return response()->json([
            'message' => 'If the account exists, a password reset link will be sent.',
        ], 202);
    }

    /**
     * Reset a password using Laravel's configured password broker.
     *
     * The shared Fortify reset action also revokes existing API tokens.
     */
    public function resetPassword(ResetPasswordRequest $request, ResetUserPassword $reset): JsonResponse
    {
        $input = $request->validated();
        $credentials = [
            'token' => is_string($input['token'] ?? null) ? $input['token'] : '',
            'email' => is_string($input['email'] ?? null) ? $input['email'] : '',
            'password' => is_string($input['password'] ?? null) ? $input['password'] : '',
            'password_confirmation' => is_string($input['password_confirmation'] ?? null)
                ? $input['password_confirmation']
                : '',
        ];

        $status = Password::broker()->reset(
            $credentials,
            function (User $user, array $credentials) use ($reset): void {
                $password = is_string($credentials['password'] ?? null) ? $credentials['password'] : '';
                $confirmation = is_string($credentials['password_confirmation'] ?? null)
                    ? $credentials['password_confirmation']
                    : '';

                $reset->reset($user, [
                    'password' => $password,
                    'password_confirmation' => $confirmation,
                ]);
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => __(is_string($status) ? $status : 'passwords.token'),
                'errors' => ['email' => [__(is_string($status) ? $status : 'passwords.token')]],
            ], 422);
        }

        return response()->json(null, 204);
    }

    /**
     * Revoke the current bearer token without affecting other devices.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(null, 204);
    }
}
