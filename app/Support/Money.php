<?php

namespace App\Support;

use InvalidArgumentException;

class Money
{
    public static function validate(string $amount, int $decimalPlaces): void
    {
        if (! preg_match('/^\d+(\.\d+)?$/', $amount)) {
            throw new InvalidArgumentException('Invalid amount.');
        }

        if (bccomp($amount, '0', 8) <= 0) {
            throw new InvalidArgumentException(
                'Amount must be greater than zero.'
            );
        }

        $parts = explode('.', $amount);

        $places = isset($parts[1])
            ? strlen($parts[1])
            : 0;

        if ($places > $decimalPlaces) {
            throw new InvalidArgumentException(
                "Amount can have maximum {$decimalPlaces} decimal places."
            );
        }
    }

    public static function add(
        string $a,
        string $b
    ): string {
        return bcadd($a, $b, 8);
    }

    public static function subtract(
        string $a,
        string $b
    ): string {
        return bcsub($a, $b, 8);
    }

    public static function format(
        string $amount,
        int $decimalPlaces
    ): string {
        return bcadd($amount, '0', $decimalPlaces);
    }
}