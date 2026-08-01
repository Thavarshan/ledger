<?php

namespace App\Http\Requests;

use App\Models\Account;

/**
 * Authorizes and validates requests that update a bank account.
 */
class UpdateAccountRequest extends AccountRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('account')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return $this->accountRules(partial: true);
    }
}
