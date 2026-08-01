<?php

namespace App\Filters;

use Filterable\Filter;
use Illuminate\Support\Carbon;

/**
 * Applies validated structured filters to a transaction query.
 */
final class TransactionFilter extends Filter
{
    /** @var list<string> */
    protected array $filters = [
        'account_id',
        'direction',
        'occurred_from',
        'occurred_to',
    ];

    /**
     * Filter transactions by account.
     */
    protected function accountId(int|string $value): void
    {
        $this->getBuilder()->where('account_id', $value);
    }

    /**
     * Filter transactions by credit or debit direction.
     */
    protected function direction(string $value): void
    {
        $this->getBuilder()->where('direction', $value);
    }

    /**
     * Filter transactions occurring on or after the supplied date.
     */
    protected function occurredFrom(string $value): void
    {
        $this->getBuilder()->where('occurred_at', '>=', Carbon::parse($value)->startOfDay());
    }

    /**
     * Filter transactions occurring on or before the supplied date.
     */
    protected function occurredTo(string $value): void
    {
        $this->getBuilder()->where('occurred_at', '<=', Carbon::parse($value)->endOfDay());
    }
}
