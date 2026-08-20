<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

/** Stores successful API mutation responses for safe client retries. */
class ApiIdempotencyKey extends Model
{
    use Prunable;

    /** @var list<string> */
    protected $fillable = [
        'user_id', 'key', 'method', 'route_name', 'request_hash',
        'response_status', 'response_body', 'response_headers', 'expires_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'response_body' => 'array',
        'response_headers' => 'array',
        'expires_at' => 'datetime',
    ];

    /** @return Builder<static> */
    public function prunable(): Builder
    {
        return static::query()->where('expires_at', '<=', now());
    }
}
