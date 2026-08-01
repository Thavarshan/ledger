<?php

namespace App\Services;

use App\Filters\TransactionFilter;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Builds the paginated transaction listing for an owner.
 */
final class TransactionIndexQuery
{
    /**
     * @return LengthAwarePaginator<int, Transaction>
     */
    public function paginate(User $owner, TransactionFilter $filter, ?string $search, ?string $sort): LengthAwarePaginator
    {
        return Transaction::query()
            ->select([
                'id', 'account_id', 'direction', 'amount_minor', 'description', 'reference', 'notes',
                'occurred_at', 'created_at', 'updated_at',
            ])
            ->ownedBy($owner)
            ->with('account:id,name,currency_code')
            ->filter($filter)
            ->search($search)
            ->sorted($sort)
            ->paginate(12)
            ->withQueryString();
    }
}
