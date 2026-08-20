<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Builds the paginated transaction listing for an owner.
 *
 * The query applies ownership, validated filters, safe sorting, and the safe
 * account projection shared by web and API transaction responses.
 */
final class TransactionIndexQuery
{
    /**
     * Build the owner's filtered, sorted, eager-loaded transaction paginator.
     *
     * Date filters are expanded to complete days so clients can request an
     * inclusive calendar range without knowing the database timestamp format.
     *
     * @param  User  $owner  The owner whose transactions may be returned.
     * @param  array<string, mixed>  $criteria
     * @param  int  $perPage  Number of rows per page after caller validation.
     * @return LengthAwarePaginator<int, Transaction>
     */
    public function paginate(User $owner, array $criteria, int $perPage = 12): LengthAwarePaginator
    {
        $accountId = $criteria['account_id'] ?? null;
        $accountId = is_int($accountId) || is_string($accountId) ? $accountId : null;
        $direction = is_string($criteria['direction'] ?? null) ? $criteria['direction'] : null;
        $occurredFrom = is_string($criteria['occurred_from'] ?? null) ? $criteria['occurred_from'] : null;
        $occurredTo = is_string($criteria['occurred_to'] ?? null) ? $criteria['occurred_to'] : null;
        $search = is_string($criteria['search'] ?? null) ? $criteria['search'] : null;
        $sort = is_string($criteria['sort'] ?? null) ? $criteria['sort'] : null;

        return Transaction::query()
            ->select([
                'id', 'account_id', 'direction', 'amount_minor', 'description', 'reference', 'notes',
                'occurred_at', 'created_at', 'updated_at',
            ])
            ->ownedBy($owner)
            ->with('account:id,name,currency_code')
            ->when($accountId, fn (Builder $query, mixed $value): Builder => $query->where('account_id', $value))
            ->when($direction, fn (Builder $query, mixed $value): Builder => $query->where('direction', $value))
            ->when($occurredFrom, fn (Builder $query, mixed $value): Builder => $query->where('occurred_at', '>=', Carbon::parse((string) $value)->startOfDay()))
            ->when($occurredTo, fn (Builder $query, mixed $value): Builder => $query->where('occurred_at', '<=', Carbon::parse((string) $value)->endOfDay()))
            ->search($search)
            ->sorted($sort)
            ->paginate($perPage)
            ->withQueryString();
    }
}
