<?php

namespace App\Enums;

use App\Concerns\HasEnumValues;

/**
 * The supported types of bank account.
 */
enum AccountType: string
{
    use HasEnumValues;

    case Savings = 'savings';
    case Current = 'current';
    case FixedDeposit = 'fixed_deposit';
}
