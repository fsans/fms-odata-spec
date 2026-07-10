<?php

declare(strict_types=1);

namespace FmsOData\Spec\Metadata;

/** Edm entity type (table definition). */
final readonly class EdmEntityType
{
    /**
     * @param list<string> $keys
     * @param list<EdmProperty> $properties
     */
    public function __construct(
        public string $name,
        public array $keys,
        public array $properties,
        public ?string $tableId = null,
        public ?string $comment = null,
    ) {}
}
