<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function Illuminate\Support\enum_value;

/** Serializes the minimal account contract embedded in transaction responses. */
class AccountOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array{id: int, name: string, currency_code: string}
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
            'currency_code' => is_string(enum_value($account->currency_code))
                ? enum_value($account->currency_code)
                : '',
        ];
    }
}
