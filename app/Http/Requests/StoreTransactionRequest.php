<?php

namespace App\Http\Requests;

use App\Models\Transaction;

/**
 * Authorizes and validates transaction creation requests.
 */
class StoreTransactionRequest extends TransactionRequest
{
    /**
     * Determine whether the user may create transactions.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Transaction::class) ?? false;
    }

    /**
     * Prepare normalized transaction values for validation.
     */
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return $this->transactionRules(partial: false);
    }
}
