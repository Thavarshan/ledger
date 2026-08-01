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
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account_id' => $this->account_id,
            'direction' => (string) enum_value($this->direction),
            'amount_minor' => (string) $this->amount_minor,
            'description' => $this->description,
            'reference' => $this->reference,
            'notes' => $this->notes,
            'occurred_at' => $this->occurred_at->toISOString(),
            'account' => $this->whenLoaded('account', fn (): array => AccountSummaryResource::make($this->account)->resolve($request)),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
