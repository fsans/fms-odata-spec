<?php

declare(strict_types=1);

namespace FmsOData\Spec\Errors;

/**
 * Static facade for error type-guard helpers.
 *
 * These mirror isFMODataError() and isFMScriptError() from the TS/Python
 * packages. In PHP, native instanceof is the idiomatic narrowing mechanism;
 * these helpers are provided for API parity.
 */
final class Errors
{
    /** Check if a value is a FileMaker OData error. */
    public static function isFMODataError(mixed $err): bool
    {
        return $err instanceof FMODataError;
    }

    /** Check if a value is a FileMaker script error. */
    public static function isFMScriptError(mixed $err): bool
    {
        return $err instanceof FMScriptError;
    }
}
