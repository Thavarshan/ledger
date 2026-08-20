<?php

namespace App\Models;

use App\Concerns\HasSearch;
use App\Concerns\HasSorting;
use App\Enums\AccountType;
use App\Enums\CurrencyCode;
use App\Enums\TransactionDirection;
use App\Http\Resources\AccountResource;
use App\Policies\AccountPolicy;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A bank account owned by a user.
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property AccountType $account_type
 * @property string|null $account_holder_name
 * @property string $bank_name
 * @property string|null $bank_code
 * @property string|null $branch_name
 * @property string|null $branch_code
 * @property string $country_code
 * @property CurrencyCode $currency_code
 * @property string|null $account_number
 * @property string|null $account_number_last4
 * @property string|null $iban
 * @property string|null $swift_bic
 * @property string|null $routing_number
 * @property string|null $sort_code
 * @property string|null $notes
 * @property bool $is_primary
 * @property bool $is_active
 * @property-read int|null $balance_minor
 * @property-read User $user
 */
#[Fillable([
    'name',
    'account_type',
    'account_holder_name',
    'bank_name',
    'bank_code',
    'branch_name',
    'branch_code',
    'country_code',
    'currency_code',
    'account_number',
    'account_number_last4',
    'iban',
    'swift_bic',
    'routing_number',
    'sort_code',
    'notes',
    'is_primary',
    'is_active',
])]
#[Hidden(['account_number', 'iban', 'routing_number', 'sort_code'])]
#[UseFactory(AccountFactory::class)]
#[UsePolicy(AccountPolicy::class)]
#[UseResource(AccountResource::class)]
class Account extends Model
{
    /**
     * @use HasFactory<AccountFactory>
     */
    use HasFactory;

    use HasSearch;
    use HasSorting;

    /**
     * Get the user that owns the account.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions recorded against the account.
     *
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scope the query to active accounts.
     *
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope the query to include the credit and debit aggregates required for
     * the derived balance.
     *
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function withBalance(Builder $query): void
    {
        $query
            ->withSum($this->creditTransactionsAggregate(), 'amount_minor')
            ->withSum($this->debitTransactionsAggregate(), 'amount_minor');
    }

    /**
     * Load the credit and debit aggregates required for the derived balance.
     */
    public function loadBalance(): static
    {
        $this
            ->loadSum($this->creditTransactionsAggregate(), 'amount_minor')
            ->loadSum($this->debitTransactionsAggregate(), 'amount_minor');

        return $this;
    }

    /**
     * Get the current balance in minor currency units.
     *
     * The attribute is available when the account is retrieved through the
     * withBalance scope or loadBalance method.
     *
     * @return Attribute<int|null, never>
     */
    protected function balanceMinor(): Attribute
    {
        return Attribute::make(
            get: static function (mixed $value, array $attributes): ?int {
                if (! array_key_exists('credit_total_minor', $attributes)
                    || ! array_key_exists('debit_total_minor', $attributes)) {
                    return null;
                }

                return self::toInteger($attributes['credit_total_minor'])
                    - self::toInteger($attributes['debit_total_minor']);
            },
        );
    }

    /**
     * Convert a database aggregate value into an integer balance component.
     *
     * PostgreSQL may return numeric aggregates as strings, while SQLite may
     * return integers. Invalid or missing values contribute zero to the balance.
     */
    private static function toInteger(mixed $value): int
    {
        return is_int($value) ? $value : (is_string($value) && is_numeric($value) ? (int) $value : 0);
    }

    /**
     * Get the non-sensitive columns available to free-text search.
     *
     * @return list<string>
     */
    protected function searchableColumns(): array
    {
        return [
            'name',
            'bank_name',
            'account_holder_name',
        ];
    }

    /**
     * Get account sort keys mapped to their database columns.
     *
     * @return array<string, string>
     */
    protected function sortableColumns(): array
    {
        return [
            'name' => 'name',
            'bank_name' => 'bank_name',
            'country_code' => 'country_code',
            'currency_code' => 'currency_code',
            'created_at' => 'created_at',
        ];
    }

    /**
     * Get the credit aggregate definition used to calculate an account balance.
     *
     * @return array<string, \Closure(Builder<Transaction>): Builder<Transaction>>
     */
    private function creditTransactionsAggregate(): array
    {
        return [
            'transactions as credit_total_minor' => fn (Builder $query): Builder => $query->where(
                'direction',
                TransactionDirection::CREDIT,
            ),
        ];
    }

    /**
     * Get the debit aggregate definition used to calculate an account balance.
     *
     * @return array<string, \Closure(Builder<Transaction>): Builder<Transaction>>
     */
    private function debitTransactionsAggregate(): array
    {
        return [
            'transactions as debit_total_minor' => fn (Builder $query): Builder => $query->where(
                'direction',
                TransactionDirection::DEBIT,
            ),
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'account_type' => AccountType::class,
            'currency_code' => CurrencyCode::class,
            'account_number' => 'encrypted',
            'iban' => 'encrypted',
            'routing_number' => 'encrypted',
            'sort_code' => 'encrypted',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
