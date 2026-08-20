<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Carbon;

/**
 * Stores successful API mutation responses for safe client retries.
 *
 * Records are owned by the authenticated user and pruned after their expiry.
 *
 * @property int $id
 * @property int $user_id
 * @property string $key
 * @property string $method
 * @property string $route_name
 * @property string $request_hash
 * @property int $response_status
 * @property array<string, mixed> $response_body
 * @property array<string, mixed> $response_headers
 * @property Carbon $expires_at
 */
class ApiIdempotencyKey extends Model
{
    use Prunable;

    /**
     * Attributes that may be written by the idempotency middleware.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id', 'key', 'method', 'route_name', 'request_hash',
        'response_status', 'response_body', 'response_headers', 'expires_at',
    ];

    /**
     * Casts for the stored response payload and expiration timestamp.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'response_body' => 'array',
        'response_headers' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * Select records whose retention window has elapsed.
     *
     * @return Builder<static>
     */
    public function prunable(): Builder
    {
        return static::query()->where('expires_at', '<=', now());
    }
}
