<?php

namespace App\Concerns;

use App\Enums\CurrencyCode;
use App\Models\Account;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Shares normalized input and validation rules between account form requests.
 */
trait AccountValidationRules
{
    protected function normalizeAccountInput(): void
    {
        $normalized = [];

        foreach (['country_code', 'currency_code', 'swift_bic'] as $field) {
            if ($this->has($field)) {
                $normalized[$field] = Str::upper((string) $this->input($field));
            }
        }

        $this->merge($normalized);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function accountRules(bool $partial = false): array
    {
        $required = $partial ? ['sometimes', 'required'] : ['required'];

        return [
            'name' => [...$required, 'string', 'max:255'],
            'account_type' => [...$required, 'string', Rule::in(Account::TYPES)],
            'account_holder_name' => ['nullable', 'string', 'max:255'],
            'bank_name' => [...$required, 'string', 'max:150'],
            'bank_code' => ['nullable', 'string', 'max:20'],
            'branch_name' => ['nullable', 'string', 'max:150'],
            'branch_code' => ['nullable', 'string', 'max:20'],
            'country_code' => [...$required, 'string', 'size:2', 'alpha'],
            'currency_code' => [...$required, 'string', Rule::in(CurrencyCode::values())],
            'account_number' => [...$required, 'string', 'max:255'],
            'iban' => ['nullable', 'string', 'max:255'],
            'swift_bic' => ['nullable', 'string', 'max:11', 'regex:/^[A-Z0-9]{8}([A-Z0-9]{3})?$/'],
            'routing_number' => ['nullable', 'string', 'max:255'],
            'sort_code' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_primary' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
