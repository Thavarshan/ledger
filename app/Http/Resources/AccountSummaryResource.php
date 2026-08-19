<?php

namespace App\Http\Resources;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function Illuminate\Support\enum_value;

/**
 * Serializes the non-sensitive account data needed by lists and transaction views.
 *
 * @mixin Account
 */
class AccountSummaryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bank_name' => $this->bank_name,
            'currency_code' => (string) enum_value($this->currency_code),
            'account_number_last4' => $this->account_number_last4,
            'is_primary' => $this->is_primary,
            'is_active' => $this->is_active,
            'balance_minor' => (string) $this->balance_minor,
        ];
    }
}
