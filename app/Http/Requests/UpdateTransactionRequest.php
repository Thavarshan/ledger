<?php

namespace App\Http\Requests;

use App\Models\Transaction;

/**
 * Authorizes and validates transaction update requests.
 */
class UpdateTransactionRequest extends TransactionRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $transaction = $this->route('transaction');

        return $this->transactionRules(
            partial: true,
            currentAccountId: $transaction instanceof Transaction ? $transaction->account_id : null,
        );
    }
}
