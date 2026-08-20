<?php

namespace App\Http\Requests;

use App\Enums\TransactionDirection;
use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Validates transaction listing, search, and ordering parameters.
 */
class IndexTransactionRequest extends FormRequest
{
    /**
     * Normalize direction filters before enum validation.
     *
     * Direction values are case-insensitive at the HTTP boundary but canonical
     * lowercase enum values are passed to the query service.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('direction')) {
            $this->merge(['direction' => Str::lower($this->string('direction')->trim()->value())]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * The account existence rule is scoped to the authenticated owner so a
     * filter cannot be used to probe another user's account IDs.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $userId = $this->user()?->getKey();

        return [
            'search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'string', 'regex:/^(?:occurred_at|amount_minor|description|created_at):(asc|desc)$/i'],
            'account_id' => [
                'nullable',
                'integer',
                Rule::exists((new Account)->getTable(), 'id')
                    ->where('user_id', is_int($userId) || is_string($userId) ? $userId : null),
            ],
            'direction' => ['nullable', Rule::enum(TransactionDirection::class)],
            'occurred_from' => ['nullable', 'date'],
            'occurred_to' => ['nullable', 'date', 'after_or_equal:occurred_from'],
        ];
    }
}
