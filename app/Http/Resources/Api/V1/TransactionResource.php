<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function Illuminate\Support\enum_value;

/**
 * Serializes transactions with their required safe account option.
 *
 * The resource fails loudly when the account relation was not eager loaded so
 * API responses cannot accidentally introduce an N+1 query.
 */
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $transaction = $this->resource;

        if (! $transaction instanceof Transaction) {
            throw new \UnexpectedValueException('Transaction resource expected.');
        }

        if (! $transaction->relationLoaded('account')) {
            throw new \LogicException('Transaction account must be eager loaded for API responses.');
        }

        return [
            'id' => $transaction->id,
            'account_id' => $transaction->account_id,
            'direction' => is_string(enum_value($transaction->direction))
                ? enum_value($transaction->direction)
                : '',
            'amount_minor' => (string) $transaction->amount_minor,
            'description' => $transaction->description,
            'reference' => $transaction->reference,
            'notes' => $transaction->notes,
            'occurred_at' => $transaction->occurred_at->toISOString(),
            'account' => AccountOptionResource::make($transaction->account)->resolve($request),
            'created_at' => $transaction->created_at?->toISOString(),
            'updated_at' => $transaction->updated_at?->toISOString(),
        ];
    }
}
