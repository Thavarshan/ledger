<?php

namespace App\Enums;

use App\Concerns\HasEnumValues;

/**
 * ISO 4217 currencies supported by the application.
 */
enum CurrencyCode: string
{
    use HasEnumValues;

    case LKR = 'LKR';
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case AUD = 'AUD';
    case CAD = 'CAD';
    case CHF = 'CHF';
    case CNY = 'CNY';
    case JPY = 'JPY';
    case SGD = 'SGD';
    case AED = 'AED';
    case SAR = 'SAR';
    case INR = 'INR';
    case NZD = 'NZD';
    case ZAR = 'ZAR';
    case HKD = 'HKD';
    case MYR = 'MYR';
    case THB = 'THB';
    case QAR = 'QAR';
    case KWD = 'KWD';
    case BHD = 'BHD';
    case OMR = 'OMR';
    case PKR = 'PKR';
    case BDT = 'BDT';
}
