<?php

declare(strict_types=1);

namespace FmsOData\Spec\Batch;

/** Overall batch result. */
final readonly class BatchResult
{
    /** @param list<BatchOpResult> $responses */
    public function __construct(
        public array $responses = [],
        public bool $ok = false,
    ) {}
}
