<?php

namespace App\Support;

use InvalidArgumentException;

final class Money
{
    public static function validateScale(
        string $amount,
        int $decimalPlaces
    ): void 
    {
        if (! preg_match('/^\d+(\.\d+)?$/', $amount)) {
            throw new InvalidArgumentException(
                'The amount must be a valid decimal number.'
            );
        }

        $parts = explode('.', $amount, 2);

        $actualDecimalPlaces = isset($parts[1])
            ? strlen($parts[1])
            : 0;

        if ($actualDecimalPlaces > $decimalPlaces) {
            throw new InvalidArgumentException(
                "The amount cannot have more than {$decimalPlaces} decimal places."
            );
        }
    }

    public static function ensurePositive(string $amount): void
    {
        if (bccomp($amount, '0', 8) !== 1) {
            throw new InvalidArgumentException(
                'The amount must be greater than zero.'
            );
        }
    }
}