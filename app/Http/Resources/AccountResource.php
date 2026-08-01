<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function Illuminate\Support\enum_value;

/**
 * Serializes an account for Inertia responses without exposing secret identifiers.
 *
 * @mixin \App\Models\Account
 */
class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Sensitive account identifiers are intentionally never serialized.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'account_type' => $this->account_type,
            'account_holder_name' => $this->account_holder_name,
            'bank_name' => $this->bank_name,
            'bank_code' => $this->bank_code,
            'branch_name' => $this->branch_name,
            'branch_code' => $this->branch_code,
            'country_code' => $this->country_code,
            'currency_code' => (string) enum_value($this->currency_code),
            'account_number_last4' => $this->account_number_last4,
            'has_iban' => filled($this->iban),
            'swift_bic' => $this->swift_bic,
            'has_routing_number' => filled($this->routing_number),
            'has_sort_code' => filled($this->sort_code),
            'notes' => $this->notes,
            'is_primary' => $this->is_primary,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
