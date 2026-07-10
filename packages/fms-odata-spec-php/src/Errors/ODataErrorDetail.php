<?php

declare(strict_types=1);

namespace FmsOData\Spec\Errors;

/** A single detail entry inside an OData error body. */
final readonly class ODataErrorDetail
{
    public function __construct(
        public string $code,
        public string $message,
        public ?string $target = null,
    ) {}
}
