<?php

namespace App\Enums;

use App\Concerns\HasEnumValues;

/**
 * Describes whether a transaction increases or decreases an account balance.
 */
enum TransactionDirection: string
{
    use HasEnumValues;

    case CREDIT = 'credit';
    case DEBIT = 'debit';
}
