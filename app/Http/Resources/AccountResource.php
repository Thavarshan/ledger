<?php

namespace App\Http\Resources;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function Illuminate\Support\enum_value;

/**
 * Serializes an account for Inertia responses without exposing secret identifiers.
 *
 * @mixin Account
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
        $account = $this->resource;

        if (! $account instanceof Account) {
            throw new \UnexpectedValueException('Account resource expected.');
        }

        $accountType = enum_value($account->account_type);
        $currencyCode = enum_value($account->currency_code);

        return [
            'id' => $account->id,
            'name' => $account->name,
            'account_type' => is_string($accountType) ? $accountType : '',
            'account_holder_name' => $account->account_holder_name,
            'bank_name' => $account->bank_name,
            'bank_code' => $account->bank_code,
            'branch_name' => $account->branch_name,
            'branch_code' => $account->branch_code,
            'country_code' => $account->country_code,
            'currency_code' => is_string($currencyCode) ? $currencyCode : '',
            'account_number_last4' => $account->account_number_last4,
            'has_iban' => filled($account->iban),
            'swift_bic' => $account->swift_bic,
            'has_routing_number' => filled($account->routing_number),
            'has_sort_code' => filled($account->sort_code),
            'notes' => $account->notes,
            'is_primary' => $account->is_primary,
            'is_active' => $account->is_active,
            'balance_minor' => $this->when(
                $account->balance_minor !== null,
                fn (): string => (string) $account->balance_minor,
            ),
            'created_at' => $account->created_at?->toISOString(),
            'updated_at' => $account->updated_at?->toISOString(),
        ];
    }
}
