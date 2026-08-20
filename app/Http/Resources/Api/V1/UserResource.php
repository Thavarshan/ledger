<?php

namespace App\Http\Resources\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes the public profile fields exposed by the v1 API.
 *
 * Authentication secrets and encrypted two-factor material remain hidden.
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->resource;

        if (! $user instanceof User) {
            throw new \UnexpectedValueException('User resource expected.');
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'two_factor_enabled' => filled($user->two_factor_confirmed_at),
            'created_at' => $user->created_at?->toISOString(),
            'updated_at' => $user->updated_at?->toISOString(),
        ];
    }
}
