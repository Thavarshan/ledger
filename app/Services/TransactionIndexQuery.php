<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Builds the paginated transaction listing for an owner.
 */
final class TransactionIndexQuery
{
    /**
     * @param  array<string, mixed>  $criteria
     * @return LengthAwarePaginator<int, Transaction>
     */
    public function paginate(User $owner, array $criteria): LengthAwarePaginator
    {
        return Transaction::query()
            ->select([
                'id', 'account_id', 'direction', 'amount_minor', 'description', 'reference', 'notes',
                'occurred_at', 'created_at', 'updated_at',
            ])
            ->ownedBy($owner)
            ->with('account:id,name,currency_code')
            ->when($criteria['account_id'] ?? null, fn (Builder $query, int|string $value): Builder => $query->where('account_id', $value))
            ->when($criteria['direction'] ?? null, fn (Builder $query, string $value): Builder => $query->where('direction', $value))
            ->when($criteria['occurred_from'] ?? null, fn (Builder $query, string $value): Builder => $query->where('occurred_at', '>=', Carbon::parse($value)->startOfDay()))
            ->when($criteria['occurred_to'] ?? null, fn (Builder $query, string $value): Builder => $query->where('occurred_at', '<=', Carbon::parse($value)->endOfDay()))
            ->search($criteria['search'] ?? null)
            ->sorted($criteria['sort'] ?? null)
            ->paginate(12)
            ->withQueryString();
    }
}
