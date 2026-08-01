<?php

namespace App\Concerns;

/**
 * Provides the scalar values for a backed enum.
 *
 * @phpstan-require-implements \BackedEnum
 */
trait HasEnumValues
{
    /**
     * Get all backed enum values.
     *
     * @return list<string|int>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
