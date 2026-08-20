<?php

namespace App\Services;

use App\Enums\TransactionDirection;
use App\Http\Resources\AccountOptionResource;
use App\Models\Transaction;
use App\Models\User;

/**
 * Supplies safe account and direction options for transaction pages.
 */
final class TransactionFormOptions
{
    /**
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
