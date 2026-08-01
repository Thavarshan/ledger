<?php

namespace App\Http\Requests;

use App\Models\Account;

/**
 * Authorizes and validates requests that create a bank account.
 */
class StoreAccountRequest extends AccountRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Account::class) ?? false;
    }

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
