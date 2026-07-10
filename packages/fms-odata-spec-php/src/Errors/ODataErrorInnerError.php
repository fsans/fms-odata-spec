<?php

declare(strict_types=1);

namespace FmsOData\Spec\Errors;

/** The innererror member of an OData error body. */
final readonly class ODataErrorInnerError
{
    public function __construct(
        public string $type,
        public string $message,
    ) {}
}
