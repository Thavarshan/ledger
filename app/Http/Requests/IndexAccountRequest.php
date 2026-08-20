<?php

namespace App\Http\Requests;

use App\Enums\AccountType;
use App\Enums\CurrencyCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Validates search and ordering parameters for the account index.
 */
class IndexAccountRequest extends FormRequest
{
    /**
     * Normalize account filters to their canonical uppercase form.
     *
     * Canonical values ensure query scopes behave identically for lowercase and
     * uppercase client input.
     */
    protected function prepareForValidation(): void
    {
        $normalized = [];

        foreach (['country_code', 'currency_code'] as $field) {
            if ($this->filled($field)) {
                $normalized[$field] = Str::upper($this->string($field)->trim()->value());
            }
        }

        $this->merge($normalized);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * These rules intentionally cover only filtering and sorting; ownership
     * and pagination limits remain responsibilities of the query service.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'string', 'regex:/^(?:name|bank_name|country_code|currency_code|created_at):(asc|desc)$/i'],
            'account_type' => ['nullable', Rule::enum(AccountType::class)],
            'country_code' => ['nullable', 'string', 'size:2', 'alpha:ascii'],
            'currency_code' => ['nullable', Rule::enum(CurrencyCode::class)],
        ];
    }
}
