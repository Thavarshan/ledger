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
     * @return array{accounts: array<int, array<string, mixed>>, directions: list<string|int>}
     */
    public function for(User $owner, ?Transaction $transaction = null): array
    {
        $accounts = $owner->accounts()
            ->select(['id', 'name', 'currency_code'])
            ->where(function ($query) use ($transaction): void {
                $query->active()
                    ->when($transaction, fn ($query) => $query->orWhere('id', $transaction->account_id));
            })
            ->orderBy('name')
            ->get();

        return [
            'accounts' => AccountOptionResource::collection($accounts)->resolve(),
            'directions' => TransactionDirection::values(),
        ];
    }
}
