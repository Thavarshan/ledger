<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function Illuminate\Support\enum_value;

/** Serializes the complete, safe account contract for API clients. */
class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $account = $this->resource;

        if (! $account instanceof Account) {
            throw new \UnexpectedValueException('Account resource expected.');
        }

        return [
            'id' => $account->id,
            'name' => $account->name,
            'account_type' => is_string(enum_value($account->account_type))
                ? enum_value($account->account_type)
                : '',
            'account_holder_name' => $account->account_holder_name,
            'bank_name' => $account->bank_name,
            'bank_code' => $account->bank_code,
            'branch_name' => $account->branch_name,
            'branch_code' => $account->branch_code,
            'country_code' => $account->country_code,
            'currency_code' => is_string(enum_value($account->currency_code))
                ? enum_value($account->currency_code)
                : '',
            'account_number_last4' => $account->account_number_last4,
            'has_iban' => filled($account->iban),
            'swift_bic' => $account->swift_bic,
            'has_routing_number' => filled($account->routing_number),
            'has_sort_code' => filled($account->sort_code),
            'notes' => $account->notes,
            'is_primary' => $account->is_primary,
            'is_active' => $account->is_active,
            'balance_minor' => (string) ($account->balance_minor ?? 0),
            'created_at' => $account->created_at?->toISOString(),
            'updated_at' => $account->updated_at?->toISOString(),
        ];
    }
}
