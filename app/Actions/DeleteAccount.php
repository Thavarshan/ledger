<?php

namespace App\Actions;

use App\Models\Account;

/**
 * Permanently removes an account and its dependent transactions.
 */
final class DeleteAccount
{
    public function handle(Account $account): void
    {
        $account->delete();
    }
}
