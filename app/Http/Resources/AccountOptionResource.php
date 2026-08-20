<?php

namespace App\Http\Resources;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function Illuminate\Support\enum_value;

/**
 * Serializes the account identity needed by transaction forms and resources.
 *
 * @mixin Account
 */
class AccountOptionResource extends JsonResource
{
    /**
     * Return the minimal account identity required by a transaction selector.
     *
     * Sensitive identifiers, balances, and ownership fields are intentionally
     * excluded so this resource can safely be nested in API responses.
     *
     * @param  Request  $request  The current request context.
     * @return array{id: int, name: string, currency_code: string}
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
            'currency_code' => is_string($currencyCode) ? $currencyCode : '',
        ];
    }
}
