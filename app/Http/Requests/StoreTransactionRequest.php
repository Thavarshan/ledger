<?php

namespace App\Http\Requests;

/**
 * Authorizes and validates transaction creation requests.
 */
class StoreTransactionRequest extends TransactionRequest
{
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
