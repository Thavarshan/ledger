<?php

namespace App\Http\Requests;

/**
 * Authorizes and validates requests that create a bank account.
 */
class StoreAccountRequest extends AccountRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return $this->accountRules(partial: false);
    }
}
