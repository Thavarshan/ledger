<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreateTokenRequest;
use App\Http\Resources\Api\V1\ApiTokenResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Manages user-owned Sanctum token metadata and integration credentials.
 *
 * Plaintext token values are returned only at creation time and never in lists.
 */
class TokenController extends Controller
{
    /**
     * List token metadata for the authenticated user.
     *
     * The response intentionally omits every token secret.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $this->user($request);

        return ApiTokenResource::collection($user->tokens()->latest()->get());
    }

    /**
     * Create a scoped integration token and expose its secret once.
     *
     * Validation prevents delegated tokens from receiving token-management
     * abilities or expiry values outside the supported integration windows.
     */
    public function store(CreateTokenRequest $request): JsonResponse
    {
        $user = $this->user($request);
        $configuredTtl = config('api.integration_token_ttl_days', 90);
        $defaultTtl = is_int($configuredTtl)
            ? $configuredTtl
            : (is_string($configuredTtl) && ctype_digit($configuredTtl) ? (int) $configuredTtl : 90);
        $days = $request->integer('expires_in_days', $defaultTtl);
        $abilities = $request->validated('abilities');
        $token = $user->createToken(
            $request->string('name')->toString(),
            is_array($abilities) ? array_values($abilities) : [],
            now()->addDays($days),
        );

        return response()->json([
            'data' => [
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'token_details' => ApiTokenResource::make($token->accessToken)->resolve($request),
            ],
        ], 201);
    }

    /**
     * Revoke an integration token owned by the authenticated user.
     */
    public function destroy(Request $request, PersonalAccessToken $token): JsonResponse
    {
        abort_unless($token->tokenable_id === $this->user($request)->getKey()
            && $token->tokenable_type === (new User)->getMorphClass(), 403);

        $token->delete();

        return response()->json(null, 204);
    }

    /**
     * Resolve the authenticated API principal for token management.
     */
    private function user(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        return $user;
    }
}
