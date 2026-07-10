<?php

declare(strict_types=1);

namespace FmsOData\Spec\Scripts;

/**
 * Static facade for script helpers and error code constants.
 *
 * Mirrors scriptPathSegment(), scriptRequestBody(), and parseScriptResponse()
 * from the TS/Python packages.
 */
final class Scripts
{
    /** Common FileMaker script error codes. */
    public const ERROR_CODES = [
        'SUCCESS' => 0,
        'RECORD_MISSING' => 101,
        'NO_RECORDS_FOUND' => 401,
        'USER_CANCELED' => 1,
        'FILE_MISSING' => 3,
        'FILE_INACCESSIBLE' => 4,
        'PASSWORD_REQUIRED' => 212,
    ];

    /** Build the URL path segment for a script invocation. */
    public static function pathSegment(ScriptIdentifier $id): string
    {
        if ($id instanceof ScriptNameId) {
            return 'Script.' . $id->name;
        }
        if ($id instanceof ScriptFmsidId) {
            return 'Script.FMSID:' . $id->id;
        }

        throw new \InvalidArgumentException(
            \sprintf('Unknown ScriptIdentifier type: %s', $id::class),
        );
    }

    /**
     * Build the request body for a script invocation.
     *
     * Returns null for no-parameter scripts (empty body). Otherwise returns
     * JSON containing scriptParameterValue, throwing on JSON failure.
     */
    public static function requestBody(?ScriptOptions $options = null): ?string
    {
        if ($options === null || $options->parameter === null) {
            return null;
        }

        $json = \json_encode(
            ['scriptParameterValue' => $options->parameter],
            \JSON_THROW_ON_ERROR,
        );

        return $json;
    }

    /**
     * Parse the raw JSON response from a script invocation into a ScriptResult.
     *
     * FMS returns a nested envelope:
     * {"scriptResult": {"code": 0, "resultParameter": "Hello World"}}
     *
     * This helper extracts code and resultParameter from the nested object.
     * A non-zero code indicates a script error. Handles missing codes,
     * non-numeric codes, non-dict input, and older fallback shapes.
     *
     * @param mixed $raw
     */
    public static function parseResponse(mixed $raw): ScriptResult
    {
        if ($raw === null || !\is_array($raw)) {
            return new ScriptResult(code: 0, raw: $raw);
        }

        $scriptResult = $raw['scriptResult'] ?? null;
        if (\is_array($scriptResult)) {
            $code = $scriptResult['code'] ?? null;
            $resultParameter = $scriptResult['resultParameter'] ?? null;

            $parsedCode = 0;
            if ($code !== null) {
                if (\is_numeric($code)) {
                    $parsedCode = (int) $code;
                }
            }

            $resultParamStr = $resultParameter !== null && (\is_string($resultParameter) || \is_scalar($resultParameter))
                ? (string) $resultParameter
                : null;

            return new ScriptResult(
                code: $parsedCode,
                resultParameter: $resultParamStr,
                raw: $raw,
            );
        }

        // Fallback for older FMS versions that may use flat shape
        return new ScriptResult(code: 0, raw: $raw);
    }
}
