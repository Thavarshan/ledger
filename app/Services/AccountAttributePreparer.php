<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Prepares account attributes before they reach an Eloquent write operation.
 *
 * This service strips fields that clients must not control and derives the
 * masked account-number suffix used by safe account resources.
 */
final class AccountAttributePreparer
{
    /**
     * Remove client-controlled derived fields and prepare masked identifiers.
     *
     * The original encrypted account number remains available to the model's
     * cast while only its final four digits are persisted for display.
     *
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
