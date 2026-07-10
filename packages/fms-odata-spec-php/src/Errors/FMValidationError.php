<?php

declare(strict_types=1);

namespace FmsOData\Spec\Errors;

/** Validation error (HTTP 400). */
class FMValidationError extends FMODataError
{
    public function __construct(
        string $message,
        ?ODataErrorBody $odataError = null,
        ?RequestRef $request = null,
    ) {
        parent::__construct($message, status: 400, odataError: $odataError, request: $request);
    }
}
