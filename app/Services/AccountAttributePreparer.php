<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Prepares derived account attributes before persistence.
 */
final class AccountAttributePreparer
{
    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function prepare(array $attributes): array
    {
        unset($attributes['account_number_last4'], $attributes['user_id']);

        if (array_key_exists('account_number', $attributes)) {
            $accountNumber = $attributes['account_number'];

            if (is_string($accountNumber)) {
                $attributes['account_number_last4'] = Str::substr($accountNumber, -4);
            }
        }

        return $attributes;
    }
}
