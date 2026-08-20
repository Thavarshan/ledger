<?php

namespace App\Http\Middleware;

use App\Models\ApiIdempotencyKey;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Persists and replays successful API create responses for retry safety.
 *
 * Failed requests leave their key available for a corrected retry, while a
 * successful request can be replayed without creating a duplicate record.
 */
class EnsureIdempotencyKey
{
    /**
     * Make account and transaction creation safe to retry.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('Idempotency-Key');

        if (! is_string($key) || ! preg_match('/^[\x21-\x7E]{8,128}$/', $key)) {
            throw new HttpResponseException(response()->json([
                'message' => 'A valid Idempotency-Key header is required.',
                'code' => 'idempotency_key_required',
            ], 400));
        }

        $user = $request->user();
        $routeName = (string) $request->route()?->getName();
        $requestHash = hash('sha256', $request->method().'|'.$routeName.'|'.$request->getContent());

        return DB::transaction(function () use ($key, $request, $next, $requestHash, $routeName, $user): Response {
            ApiIdempotencyKey::query()->where('expires_at', '<=', now())->delete();

            ApiIdempotencyKey::query()->insertOrIgnore([
                'user_id' => $user?->getAuthIdentifier(),
                'key' => $key,
                'method' => $request->method(),
                'route_name' => $routeName,
                'request_hash' => $requestHash,
                'response_status' => 0,
                'response_body' => json_encode([]),
                'expires_at' => now()->addDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $record = ApiIdempotencyKey::query()
                ->where('user_id', $user?->getAuthIdentifier())
                ->where('key', $key)
                ->lockForUpdate()
                ->firstOrFail();

            if ($record->request_hash !== $requestHash || $record->method !== $request->method() || $record->route_name !== $routeName) {
                throw new HttpResponseException(response()->json([
                    'message' => 'The Idempotency-Key was already used for a different request.',
                    'code' => 'idempotency_key_reused',
                ], 409));
            }

            if ($record->response_status !== 0) {
                $replay = response()
                    ->json($record->response_body, $record->response_status)
                    ->header('Idempotent-Replayed', 'true');

                foreach ($record->response_headers ?? [] as $header => $value) {
                    if (is_scalar($value)) {
                        $replay->header((string) $header, (string) $value);
                    }
                }

                return $replay;
            }

            $response = $next($request);

            if ($response->isSuccessful()) {
                $body = json_decode($response->getContent() ?: '', true);

                $record->forceFill([
                    'response_status' => $response->getStatusCode(),
                    'response_body' => is_array($body) ? $body : [],
                    'response_headers' => array_filter([
                        'Location' => $response->headers->get('Location'),
                    ]),
                ])->save();
            }

            return $response;
        });
    }
}
