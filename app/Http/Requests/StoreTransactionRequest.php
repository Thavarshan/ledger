<?php

namespace App\Http\Requests;

/**
 * Authorizes and validates transaction creation requests.
 */
class StoreTransactionRequest extends TransactionRequest
{
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
