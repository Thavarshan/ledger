<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\AccountType;
use App\Enums\CurrencyCode;
use App\Enums\TransactionDirection;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Provides enum metadata required to build API client forms.
 *
 * Keeping these values server-owned prevents clients from duplicating domain
 * enum definitions that may change in a future API version.
 */
class MetadataController extends Controller
{
    /**
     * Return enum values needed by external clients.
     */
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => [
                'account_types' => AccountType::values(),
                'currencies' => CurrencyCode::values(),
                'transaction_directions' => TransactionDirection::values(),
            ],
        ]);
    }
}
