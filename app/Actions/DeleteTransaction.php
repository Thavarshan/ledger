<?php

namespace App\Actions;

use App\Models\Transaction;

/**
 * Permanently removes a transaction.
 */
final class DeleteTransaction
{
    public function handle(Transaction $transaction): void
    {
        $transaction->delete();
    }
}
