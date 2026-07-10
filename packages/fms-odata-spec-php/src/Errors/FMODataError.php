<?php

declare(strict_types=1);

namespace FmsOData\Spec\Errors;

/**
 * Base error class for all FileMaker OData errors.
 *
 * Use native instanceof as the normal PHP narrowing mechanism. The optional
 * isFMODataError() / isFMScriptError() helpers are provided for parity with
 * the TS/Python packages but do not replace normal exception typing.
 *
 * Note: PHP's Exception class already has a protected int $code property.
 * The OData error code (a string) is exposed as $odataCode to avoid the
 * type conflict.
 */
class FMODataError extends \Exception
{
    /** HTTP status code. */
    public readonly int $status;

    /** OData error code (if available). String to match the TS/Python API. */
    public readonly ?string $odataCode;

    /** OData error object (if available in response body). */
    public readonly ?ODataErrorBody $odataError;

    /** The request that caused the error. */
    public readonly ?RequestRef $request;

    /**
     * @param string $message Error message.
     * @param int $status HTTP status code.
     * @param ?string $odataCode OData error code.
     * @param ?ODataErrorBody $odataError OData error body.
     * @param ?RequestRef $request Request reference.
     */
    public function __construct(
        string $message,
        int $status,
        ?string $odataCode = null,
        ?ODataErrorBody $odataError = null,
        ?RequestRef $request = null,
    ) {
        parent::__construct($message);
        $this->status = $status;
        $this->odataCode = $odataCode;
        $this->odataError = $odataError;
        $this->request = $request;
    }
}
