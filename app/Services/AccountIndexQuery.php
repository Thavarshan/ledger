<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Builds the paginated account listing for an owner.
 *
 * Filtering, safe sorting, balance aggregation, and query-string preservation
 * live here so controllers only coordinate authorization and presentation.
 */
final class AccountIndexQuery
{
    /**
     * Build the owner's filtered, sorted, balance-aware account paginator.
     *
     * Criteria are already validated by the caller; this service still checks
     * scalar types so API and web entry points share a defensive boundary.
     *
     * @param  User  $owner  The account owner whose records may be returned.
     * @param  array<string, mixed>  $criteria
     * @param  int  $perPage  Number of rows per page after caller validation.
     * @return LengthAwarePaginator<int, Account>
     */
    public function paginate(User $owner, array $criteria, int $perPage = 12): LengthAwarePaginator
    {
        $accountType = is_string($criteria['account_type'] ?? null) ? $criteria['account_type'] : null;
        $countryCode = is_string($criteria['country_code'] ?? null) ? $criteria['country_code'] : null;
        $currencyCode = is_string($criteria['currency_code'] ?? null) ? $criteria['currency_code'] : null;
        $search = is_string($criteria['search'] ?? null) ? $criteria['search'] : null;
        $sort = is_string($criteria['sort'] ?? null) ? $criteria['sort'] : null;
        $isActive = is_bool($criteria['is_active'] ?? null) ? $criteria['is_active'] : null;

        return Account::query()
            ->select([
                'id', 'user_id', 'name', 'account_type', 'bank_name', 'country_code',
                'currency_code', 'account_number_last4', 'is_primary', 'is_active', 'created_at',
            ])
            ->whereBelongsTo($owner)
            ->when($accountType, fn (Builder $query, mixed $value): Builder => $query->where('account_type', $value))
            ->when($countryCode, fn (Builder $query, mixed $value): Builder => $query->where('country_code', $value))
            ->when($currencyCode, fn (Builder $query, mixed $value): Builder => $query->where('currency_code', $value))
            ->when($isActive !== null, fn (Builder $query): Builder => $query->where('is_active', $isActive))
            ->search($search)
            ->sorted($sort)
            ->withBalance()
            ->paginate($perPage)
            ->withQueryString();
    }
}
