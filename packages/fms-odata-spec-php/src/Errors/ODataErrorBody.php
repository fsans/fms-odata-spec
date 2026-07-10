<?php

declare(strict_types=1);

namespace FmsOData\Spec\Errors;

/** OData standard error response body. */
final readonly class ODataErrorBody
{
    public function __construct(
        public ODataErrorInner $error,
    ) {}
}
