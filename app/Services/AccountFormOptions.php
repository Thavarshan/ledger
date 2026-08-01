<?php

namespace App\Services;

use App\Enums\AccountType;
use App\Enums\CurrencyCode;

/**
 * Supplies stable option values for account forms.
 */
final class AccountFormOptions
{
    /**
     * @return array{accountTypes: list<string|int>, currencies: list<string|int>}
     */
    public function all(): array
    {
        return [
            'accountTypes' => AccountType::values(),
            'currencies' => CurrencyCode::values(),
        ];
    }
}
