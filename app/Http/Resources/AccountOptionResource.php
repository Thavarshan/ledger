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
     * @return array{id: int, name: string, currency_code: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'currency_code' => (string) enum_value($this->currency_code),
        ];
    }
}
