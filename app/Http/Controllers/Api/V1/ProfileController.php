<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PasswordUpdateRequest;
use App\Http\Requests\Api\V1\ProfileDeleteRequest;
use App\Http\Requests\Api\V1\ProfileUpdateRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Handles authenticated profile reads, updates, and deletion. */
class ProfileController extends Controller
{
    /** Return the authenticated user's public profile. */
    public function show(Request $request): UserResource
    {
        return UserResource::make($this->user($request));
    }

    /** Update the authenticated user's profile. */
    public function update(ProfileUpdateRequest $request): UserResource
    {
        $user = $this->user($request);
        $user->update($request->validated());

        return UserResource::make($user->refresh());
    }

    /** Change the password and revoke all bearer tokens. */
    public function updatePassword(PasswordUpdateRequest $request): JsonResponse
    {
        $user = $this->user($request);

        DB::transaction(function () use ($request, $user): void {
            $user->forceFill(['password' => $request->validated('password')])->save();
            $user->tokens()->delete();
        });

        return response()->json(null, 204);
    }

    /** Delete the authenticated user's profile and owned data. */
    public function destroy(ProfileDeleteRequest $request): JsonResponse
    {
        $user = $this->user($request);

        DB::transaction(function () use ($user): void {
            $user->tokens()->delete();
            $user->delete();
        });

        return response()->json(null, 204);
    }

    /** Resolve the authenticated API principal for profile operations. */
    private function user(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        return $user;
    }
}
