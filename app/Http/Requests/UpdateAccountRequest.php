<?php

namespace App\Http\Requests;

use App\Concerns\AccountValidationRules;
use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Authorizes and validates requests that update a bank account.
 */
class UpdateAccountRequest extends FormRequest
{
    use AccountValidationRules;

    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('account')) ?? false;
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
        return $this->accountRules(partial: true);
    }
}
