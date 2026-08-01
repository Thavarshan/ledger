<?php

namespace App\Filters;

use Filterable\Filter;

/**
 * Applies validated, structured filters to an account query.
 */
final class AccountFilter extends Filter
{
    /** @var list<string> */
    protected array $filters = [
        'account_type',
        'country_code',
        'currency_code',
    ];

    /**
     * Filter accounts by their type.
     */
    protected function accountType(string $value): void
    {
        $this->getBuilder()->where('account_type', $value);
    }

    /**
     * Filter accounts by ISO 3166-1 alpha-2 country code.
     */
    protected function countryCode(string $value): void
    {
        $this->getBuilder()->where('country_code', $value);
    }

    /**
     * Filter accounts by ISO 4217 currency code.
     */
    protected function currencyCode(string $value): void
    {
        $this->getBuilder()->where('currency_code', $value);
    }
}
