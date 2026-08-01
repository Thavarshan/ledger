<?php

namespace App\Http\Requests;

use App\Concerns\AccountValidationRules;
use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Authorizes and validates requests that create a bank account.
 */
class StoreAccountRequest extends FormRequest
{
    use AccountValidationRules;

    public function authorize(): bool
    {
        return $this->user()?->can('create', Account::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeAccountInput();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return $this->accountRules();
    }
}
