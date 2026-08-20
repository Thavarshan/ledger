<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Serializes token metadata without exposing token secrets.
 *
 * Plaintext Sanctum values are intentionally available only in the create
 * response and are never reconstructed from this resource.
 */
class ApiTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $token = $this->resource;

        if (! $token instanceof PersonalAccessToken) {
            throw new \UnexpectedValueException('API token resource expected.');
        }

        return [
            'id' => $token->id,
            'name' => $token->name,
            'abilities' => $token->abilities ?? [],
            'last_used_at' => $token->last_used_at?->toISOString(),
            'expires_at' => $token->expires_at?->toISOString(),
            'created_at' => $token->created_at?->toISOString(),
        ];
    }
}
