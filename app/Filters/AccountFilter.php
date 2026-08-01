<?php

namespace App\Filters;

use App\Enums\CurrencyCode;
use App\Models\Account;
use Filterable\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
     * Create a new account filter.
     */
    public function __construct(Request $request)
    {
        $request->merge([
            'country_code' => $request->filled('country_code')
                ? Str::upper((string) $request->input('country_code'))
                : $request->input('country_code'),
            'currency_code' => $request->filled('currency_code')
                ? Str::upper((string) $request->input('currency_code'))
                : $request->input('currency_code'),
        ]);

        parent::__construct($request);

        $this->enableFeature('validation')
            ->setValidationRules([
                'account_type' => ['nullable', Rule::in(Account::TYPES)],
                'country_code' => ['nullable', 'string', 'size:2', 'alpha:ascii'],
                'currency_code' => ['nullable', Rule::in(CurrencyCode::values())],
            ]);
    }

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
