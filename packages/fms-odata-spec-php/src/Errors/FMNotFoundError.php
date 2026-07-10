<?php

declare(strict_types=1);

namespace FmsOData\Spec\Errors;

/** Not found error (HTTP 404). */
class FMNotFoundError extends FMODataError
{
    public function __construct(string $message, ?RequestRef $request = null)
    {
        parent::__construct($message, status: 404, request: $request);
    }
}
