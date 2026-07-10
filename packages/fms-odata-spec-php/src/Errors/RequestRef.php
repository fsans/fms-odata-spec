<?php

declare(strict_types=1);

namespace FmsOData\Spec\Errors;

/** Reference to the request that caused an error. */
final readonly class RequestRef
{
    public function __construct(
        public string $method,
        public string $url,
    ) {}
}
