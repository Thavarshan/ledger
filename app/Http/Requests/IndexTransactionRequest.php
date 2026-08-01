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
    protected function prepareForValidation(): void
    {
        if ($this->has('direction')) {
            $this->merge(['direction' => Str::lower((string) $this->input('direction'))]);
        }
    }

    /**
     * Determine whether the user may view transaction listings and forms.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'string', 'regex:/^(?:occurred_at|amount_minor|description|created_at):(asc|desc)$/i'],
            'account_id' => [
                'nullable',
                'integer',
                Rule::exists((new Account)->getTable(), 'id')
                    ->where('user_id', $this->user()?->getKey()),
            ],
            'direction' => ['nullable', Rule::enum(TransactionDirection::class)],
            'occurred_from' => ['nullable', 'date'],
            'occurred_to' => ['nullable', 'date', 'after_or_equal:occurred_from'],
        ];
    }
}
