<?php

namespace App\Services;

use App\Enums\TransactionDirection;
use App\Http\Resources\AccountOptionResource;
use App\Models\Transaction;
use App\Models\User;

/**
 * Supplies safe account and direction options for transaction forms.
 *
 * Active accounts are offered for new transactions. During editing, the
 * current account remains selectable so an existing record can be saved even
 * if that account was archived after the transaction was created.
 */
final class TransactionFormOptions
{
    /**
     * Build account and direction options for transaction forms.
     *
     * The account resource deliberately exposes only the identity and currency
     * required to render a safe selector.
     *
     * @param  User  $owner  The authenticated user whose accounts are listed.
     * @param  Transaction|null  $transaction  Optional transaction being edited.
     * @return array{accounts: array<int|string, mixed>, directions: list<string|int>}
     */
    public function for(User $owner, ?Transaction $transaction = null): array
    {
        $transactionAccountId = $transaction?->account_id;

        $accounts = $owner->accounts()
            ->select(['id', 'name', 'currency_code'])
            ->where(function ($query) use ($transactionAccountId): void {
                $query->active()
                    ->when($transactionAccountId !== null, fn ($query) => $query->orWhere('id', $transactionAccountId));
            })
            ->orderBy('name')
            ->get();

        return [
            'accounts' => AccountOptionResource::collection($accounts)->resolve(),
            'directions' => TransactionDirection::values(),
        ];
    }
}
