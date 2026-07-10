<?php

declare(strict_types=1);

namespace FmsOData\Spec\Metadata;

/** Edm entity set (table occurrence). */
final readonly class EdmEntitySet
{
    public function __construct(
        public string $name,
        public string $entityType,
    ) {}
}
