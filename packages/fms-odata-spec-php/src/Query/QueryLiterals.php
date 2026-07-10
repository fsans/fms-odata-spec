<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/**
 * Static facade for OData literal formatting helpers.
 *
 * Mirrors escapeStringLiteral() and formatLiteral() from the TS/Python
 * packages.
 */
final class QueryLiterals
{
    /**
     * Escape single quotes in OData string literals.
     *
     * OData requires 'O''Brien' (doubled quotes) for names containing
     * apostrophes.
     */
    public static function escapeStringLiteral(string $s): string
    {
        return \str_replace("'", "''", $s);
    }

    /**
     * Format a primitive value as an OData literal for use in $filter.
     *
     * Strings are single-quoted with escaped internal quotes. Numbers and
     * booleans are raw. DateTimes are ISO-8601 with microseconds stripped
     * (matching the TS Date.toISOString() behaviour with millis removed).
     *
     * PHP's DateTime always carries a timezone (unlike Python's naive
     * datetime). All DateTimeInterface values therefore include a timezone
     * suffix: 'Z' for UTC, or a +/- offset for other timezones.
     *
     * @param string|int|float|bool|\DateTimeInterface $value
     */
    public static function formatLiteral(string|int|float|bool|\DateTimeInterface $value): string
    {
        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (\is_int($value) || \is_float($value)) {
            return (string) $value;
        }
        if (\is_string($value)) {
            return "'" . self::escapeStringLiteral($value) . "'";
        }

        // $value is \DateTimeInterface (the only remaining type in the union)
        $iso = $value->format('Y-m-d\TH:i:s');
        $tz = $value->getTimezone();
        $offset = $tz->getOffset($value);
        if ($offset === 0) {
            return $iso . 'Z';
        }

        // Format with timezone offset
        $offsetHours = (int) ($offset / 3600);
        $offsetMinutes = (int) (\abs($offset) / 60 % 60);
        $sign = $offset >= 0 ? '+' : '-';
        $tzStr = \sprintf('%s%02d:%02d', $sign, \abs($offsetHours), $offsetMinutes);

        return $iso . $tzStr;
    }
}
