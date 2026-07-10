<?php

declare(strict_types=1);

namespace FmsOData\Spec\Batch;

/** A changeset is a group of atomic write operations. */
final readonly class Changeset
{
    /** @param list<BatchOperation> $operations */
    public function __construct(
        public array $operations = [],
    ) {}
}
