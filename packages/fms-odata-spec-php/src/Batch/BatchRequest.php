<?php

declare(strict_types=1);

namespace FmsOData\Spec\Batch;

/** A batch request consists of retrieve operations and changesets. */
final readonly class BatchRequest
{
    /**
     * @param list<BatchOperation> $retrieveOps Retrieve operations (GET) — executed outside changesets.
     * @param list<Changeset> $changesets Changesets (atomic write groups).
     */
    public function __construct(
        public array $retrieveOps = [],
        public array $changesets = [],
    ) {}
}
