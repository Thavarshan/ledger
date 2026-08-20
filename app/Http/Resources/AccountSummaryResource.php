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
     * Return the deterministic account listing contract.
     *
     * The derived balance is always present for list consumers, while account
     * numbers remain limited to the persisted final-four mask.
     *
     * @param  Request  $request  The current request context.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $account = $this->resource;

        if (! $account instanceof Account) {
            throw new \UnexpectedValueException('Account resource expected.');
        }

        $currencyCode = enum_value($account->currency_code);

        return [
            'id' => $account->id,
            'name' => $account->name,
            'bank_name' => $account->bank_name,
            'currency_code' => is_string($currencyCode) ? $currencyCode : '',
            'account_number_last4' => $account->account_number_last4,
            'is_primary' => $account->is_primary,
            'is_active' => $account->is_active,
            'balance_minor' => (string) ($account->balance_minor ?? 0),
        ];
    }
}
