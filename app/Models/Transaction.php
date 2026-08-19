<?php

namespace App\Models;

use App\Concerns\HasSearch;
use App\Concerns\HasSorting;
use App\Enums\TransactionDirection;
use App\Http\Resources\TransactionResource;
use App\Policies\TransactionPolicy;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A credit or debit transaction belonging to a bank account.
 *
 * @property int $id
 * @property int $account_id
 * @property TransactionDirection $direction
 * @property int $amount_minor
 * @property string $description
 * @property string|null $reference
 * @property string|null $notes
 * @property Carbon $occurred_at
 * @property-read Account $account
 */
#[Fillable([
    'account_id',
    'direction',
    'amount_minor',
    'description',
    'reference',
    'notes',
    'occurred_at',
])]
#[UseFactory(TransactionFactory::class)]
#[UsePolicy(TransactionPolicy::class)]
#[UseResource(TransactionResource::class)]
class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    use HasSearch;
    use HasSorting;

    /**
     * Get the account that owns the transaction.
     *
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Scope the query to transactions belonging to an account owner.
     *
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function ownedBy(Builder $query, User $user): void
    {
        $query->whereHas('account', fn (Builder $accountQuery): Builder => $accountQuery->whereBelongsTo($user));
    }

    /**
     * Get the non-sensitive columns available to free-text search.
     *
     * @return list<string>
     */
    protected function searchableColumns(): array
    {
        return ['description', 'reference'];
    }

    /**
     * Get public sort keys mapped to database columns.
     *
     * @return array<string, string>
     */
    protected function sortableColumns(): array
    {
        return [
            'occurred_at' => 'occurred_at',
            'amount_minor' => 'amount_minor',
            'description' => 'description',
            'created_at' => 'created_at',
        ];
    }

    /**
     * Get the default transaction ordering column.
     */
    protected function defaultSortColumn(): string
    {
        return 'occurred_at';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'direction' => TransactionDirection::class,
            'amount_minor' => 'integer',
            'occurred_at' => 'immutable_datetime',
        ];
    }
}
