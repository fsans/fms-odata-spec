<?php

declare(strict_types=1);

namespace FmsOData\Spec\Errors;

/** Authentication error (HTTP 401). */
class FMAuthError extends FMODataError
{
    public function __construct(string $message, ?RequestRef $request = null)
    {
        parent::__construct($message, status: 401, request: $request);
    }
}
