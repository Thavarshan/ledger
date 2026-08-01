<?php

namespace App\Http\Requests;

use App\Enums\TransactionDirection;
use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Provides normalized, shared validation for transaction mutations.
 */
abstract class TransactionRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $normalized = [];

        if ($this->has('direction')) {
            $normalized['direction'] = Str::lower((string) $this->input('direction'));
        }

        if ($this->has('occurred_at')) {
            $normalized['occurred_at'] = trim((string) $this->input('occurred_at'));
        }

        $this->merge($normalized);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function transactionRules(bool $partial, ?int $currentAccountId = null): array
    {
        $required = $partial ? ['sometimes', 'required'] : ['required'];

        return [
            'account_id' => [
                ...$required,
                'integer',
                Rule::exists((new Account)->getTable(), 'id')
                    ->where('user_id', $this->user()?->getKey())
                    ->where(function ($query) use ($currentAccountId): void {
                        $query->where('is_active', true)
                            ->when($currentAccountId, fn ($query) => $query->orWhere('id', $currentAccountId));
                    }),
            ],
            'direction' => [...$required, 'string', Rule::enum(TransactionDirection::class)],
            'amount_minor' => [...$required, 'integer', 'min:1'],
            'description' => [...$required, 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'occurred_at' => [...$required, 'date'],
        ];
    }
}
