<?php

declare(strict_types=1);

namespace FmsOData\Spec\Scripts;

/** Inner scriptResult object from the OData API. */
final readonly class ScriptResultInner
{
    public function __construct(
        public int $code,
        public ?string $resultParameter = null,
    ) {}
}
