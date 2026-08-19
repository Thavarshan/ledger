<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Builds the paginated account listing for an owner.
 */
final class AccountIndexQuery
{
    /**
     * @param  array<string, mixed>  $criteria
     * @return LengthAwarePaginator<int, Account>
     */
    public function paginate(User $owner, array $criteria): LengthAwarePaginator
    {
        return Account::query()
            ->select([
                'id', 'user_id', 'name', 'account_type', 'bank_name', 'country_code',
                'currency_code', 'account_number_last4', 'is_primary', 'is_active', 'created_at',
            ])
            ->whereBelongsTo($owner)
            ->when($criteria['account_type'] ?? null, fn (Builder $query, string $value): Builder => $query->where('account_type', $value))
            ->when($criteria['country_code'] ?? null, fn (Builder $query, string $value): Builder => $query->where('country_code', $value))
            ->when($criteria['currency_code'] ?? null, fn (Builder $query, string $value): Builder => $query->where('currency_code', $value))
            ->search($criteria['search'] ?? null)
            ->sorted($criteria['sort'] ?? null)
            ->withBalance()
            ->paginate(12)
            ->withQueryString();
    }
}
