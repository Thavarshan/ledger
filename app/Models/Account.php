<?php

namespace App\Models;

use App\Concerns\HasSearch;
use App\Concerns\HasSorting;
use App\Enums\CurrencyCode;
use App\Http\Resources\AccountResource;
use App\Policies\AccountPolicy;
use Database\Factories\AccountFactory;
use Filterable\Traits\Filterable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A bank account owned by a user.
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $account_type
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
    public const array TYPES = [
        'savings',
        'current',
        'fixed_deposit',
    ];

    /** @use HasFactory<AccountFactory> */
    use HasFactory;
    use HasSearch;
    use HasSorting;
    use Filterable;

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
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
