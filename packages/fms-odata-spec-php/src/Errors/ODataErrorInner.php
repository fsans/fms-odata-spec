<?php

declare(strict_types=1);

namespace FmsOData\Spec\Errors;

/** The error member of an OData error body. */
final readonly class ODataErrorInner
{
    /**
     * @param list<ODataErrorDetail> $details
     */
    public function __construct(
        public string $code,
        public string $message,
        public ?string $target = null,
        public array $details = [],
        public ?ODataErrorInnerError $innererror = null,
    ) {}
}
