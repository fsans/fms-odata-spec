<?php

declare(strict_types=1);

namespace FmsOData\Spec\Batch;

/** A single operation in a batch request. */
final readonly class BatchOperation
{
    /**
     * @param array<string, mixed>|null $body Request body (for create, patch, put).
     * @param array<string, mixed>|null $query Query parameters (for list, get).
     */
    public function __construct(
        public BatchOpType $op,
        public string $entitySet,
        public string|int|null $key = null,
        public ?array $body = null,
        public ?array $query = null,
        public ?int $contentId = null,
    ) {}
}
