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
    /**
     * Normalize direction and timestamp input before validation.
     *
     * Normalization keeps the persisted enum and immutable timestamp values
     * consistent regardless of client casing or display format.
     */
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
     * Build the validation rules shared by transaction creation and updates.
     *
     * Account ownership is constrained in the database rule, while updates
     * may retain the transaction's current account even after deactivation.
     *
     * @param  bool  $partial  Whether fields may be omitted during an update.
     * @param  int|null  $currentAccountId  Account allowed for an existing row.
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
