<?php

namespace App\Services;

use App\Filters\AccountFilter;
use App\Models\Account;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Builds the paginated account listing for an owner.
 */
final class AccountIndexQuery
{
    /**
     * @return LengthAwarePaginator<int, Account>
     */
    public function paginate(User $owner, AccountFilter $filter, ?string $search, ?string $sort): LengthAwarePaginator
    {
        return Account::query()
            ->select([
                'id', 'user_id', 'name', 'account_type', 'bank_name', 'country_code',
                'currency_code', 'account_number_last4', 'is_primary', 'is_active', 'created_at',
            ])
            ->whereBelongsTo($owner)
            ->filter($filter)
            ->search($search)
            ->sorted($sort)
            ->paginate(12)
            ->withQueryString();
    }
}
