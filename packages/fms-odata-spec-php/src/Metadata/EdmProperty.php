<?php

declare(strict_types=1);

namespace FmsOData\Spec\Metadata;

/** Edm property (field) in an entity type. */
final readonly class EdmProperty
{
    public function __construct(
        public string $name,
        public string $type,
        public ?bool $nullable = null,
        public ?int $maxLength = null,
        public ?bool $isKey = null,
        public ?int $repetitions = null,
        public ?FMAnnotations $annotations = null,
    ) {}
}
