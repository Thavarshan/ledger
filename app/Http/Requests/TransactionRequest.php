<?php

namespace App\Http\Requests;

use App\Enums\TransactionDirection;
use App\Models\Account;
use Illuminate\Database\Query\Builder;
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

        if ($this->filled('direction')) {
            $normalized['direction'] = Str::lower($this->string('direction')->trim()->value());
        }

        if ($this->filled('occurred_at')) {
            $normalized['occurred_at'] = $this->string('occurred_at')->trim()->value();
        }

        $this->merge($normalized);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function transactionRules(bool $partial, ?int $currentAccountId = null): array
    {
        $required = $partial ? ['sometimes', 'required'] : ['required'];
        $userId = $this->user()?->getKey();

        return [
            'account_id' => [
                ...$required,
                'integer',
                Rule::exists((new Account)->getTable(), 'id')
                    ->where('user_id', is_int($userId) || is_string($userId) ? $userId : null)
                    ->where(function (Builder $query) use ($currentAccountId): void {
                        $query->where('is_active', true)
                            ->when($currentAccountId, fn (Builder $query): Builder => $query->orWhere('id', $currentAccountId));
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
