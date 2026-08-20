<?php

namespace App\Http\Requests;

use App\Enums\AccountType;
use App\Enums\CurrencyCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Provides normalized, shared validation for account mutations.
 */
abstract class AccountRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $normalized = [];

        foreach (['country_code', 'currency_code', 'swift_bic'] as $field) {
            if ($this->filled($field)) {
                $normalized[$field] = Str::upper($this->string($field)->trim()->value());
            }
        }

        $this->merge($normalized);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function accountRules(bool $partial): array
    {
        $required = $partial ? ['sometimes', 'required'] : ['required'];

        return [
            'name' => [...$required, 'string', 'max:255'],
            'account_type' => [...$required, 'string', Rule::enum(AccountType::class)],
            'account_holder_name' => ['nullable', 'string', 'max:255'],
            'bank_name' => [...$required, 'string', 'max:150'],
            'bank_code' => ['nullable', 'string', 'max:20'],
            'branch_name' => ['nullable', 'string', 'max:150'],
            'branch_code' => ['nullable', 'string', 'max:20'],
            'country_code' => [...$required, 'string', 'size:2', 'alpha:ascii'],
            'currency_code' => [...$required, 'string', Rule::enum(CurrencyCode::class)],
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
