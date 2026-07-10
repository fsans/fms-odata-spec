<?php

declare(strict_types=1);

namespace FmsOData\Spec\Schema;

/** FileMaker field types for table creation. */
enum FMFieldType: string
{
    case NUMERIC = 'NUMERIC';
    case DECIMAL = 'DECIMAL';
    case INT = 'INT';
    case DATE = 'DATE';
    case TIME = 'TIME';
    case TIMESTAMP = 'TIMESTAMP';
    case VARCHAR = 'VARCHAR';
    case CHARACTER_VARYING = 'CHARACTER VARYING';
    case BLOB = 'BLOB';
    case VARBINARY = 'VARBINARY';
    case LONGVARBINARY = 'LONGVARBINARY';
    case BINARY_VARYING = 'BINARY VARYING';

    /** @return list<FMFieldType> */
    public static function all(): array
    {
        return [
            self::NUMERIC, self::DECIMAL, self::INT, self::DATE, self::TIME, self::TIMESTAMP,
            self::VARCHAR, self::CHARACTER_VARYING, self::BLOB, self::VARBINARY,
            self::LONGVARBINARY, self::BINARY_VARYING,
        ];
    }
}
