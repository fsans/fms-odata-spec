<?php

declare(strict_types=1);

namespace FmsOData\Spec\Scripts;

/** Parsed script result. */
final readonly class ScriptResult
{
    /**
     * @param mixed $raw Raw response data.
     * @param ?string $resultParameter Text result from Exit Script step.
     */
    public function __construct(
        public int $code,
        public mixed $raw,
        public ?string $resultParameter = null,
    ) {}
}
