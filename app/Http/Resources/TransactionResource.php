<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function Illuminate\Support\enum_value;

/**
 * Serializes transaction data without exposing unrelated account details.
 *
 * @mixin Transaction
 */
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * The nested account is emitted only when the controller eager-loads it,
     * which prevents accidental N+1 queries and keeps the contract explicit.
     *
     * @param  Request  $request  The current request context.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $transaction = $this->resource;

        if (! $transaction instanceof Transaction) {
            throw new \UnexpectedValueException('Transaction resource expected.');
        }

        $direction = enum_value($transaction->direction);

        return [
            'id' => $transaction->id,
            'account_id' => $transaction->account_id,
            'direction' => is_string($direction) ? $direction : '',
            'amount_minor' => (string) $transaction->amount_minor,
            'description' => $transaction->description,
            'reference' => $transaction->reference,
            'notes' => $transaction->notes,
            'occurred_at' => $transaction->occurred_at->toISOString(),
            'account' => $this->whenLoaded(
                'account',
                fn (): array => AccountOptionResource::make($transaction->account)->resolve($request),
            ),
            'created_at' => $transaction->created_at?->toISOString(),
            'updated_at' => $transaction->updated_at?->toISOString(),
        ];
    }
}
