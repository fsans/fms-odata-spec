<?php

declare(strict_types=1);

namespace FmsOData\Spec\Schema;

/**
 * Static facade for schema modification (DDL) helpers.
 *
 * Mirrors FIELD_TYPES, FIELD_DEFAULTS, and parseFieldType() from the
 * TS/Python packages.
 */
final class Schema
{
    /** All supported default value expressions. */
    public const FIELD_DEFAULTS = [
        'USER', 'USERNAME', 'CURRENT_USER',
        'CURRENT_DATE', 'CURDATE',
        'CURRENT_TIME', 'CURTIME',
        'CURRENT_TIMESTAMP', 'CURTIMESTAMP',
    ];

    /**
     * Parse a field type string to extract the base type, length, and repetitions.
     *
     * Handles patterns like "VARCHAR(200)", "INT[4]", "VARCHAR(200)[4]".
     * Also handles multi-word types such as "CHARACTER VARYING".
     */
    public static function parseFieldType(string $typeStr): ParsedFieldType
    {
        $length = null;
        $repetitions = null;

        if (\preg_match('/\((\d+)\)/', $typeStr, $lengthMatch)) {
            $length = (int) $lengthMatch[1];
        }
        if (\preg_match('/\[(\d+)\]/', $typeStr, $repMatch)) {
            $repetitions = (int) $repMatch[1];
        }

        // Strip length and repetitions from the base type
        $baseType = \preg_replace('/\(.*?\)/', '', $typeStr) ?? $typeStr;
        $baseType = \preg_replace('/\[.*?\]/', '', $baseType) ?? $baseType;
        $baseType = \trim($baseType);

        return new ParsedFieldType(
            baseType: $baseType,
            length: $length,
            repetitions: $repetitions,
        );
    }
}
