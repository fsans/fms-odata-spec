<?php

declare(strict_types=1);

namespace FmsOData\Spec\Metadata;

/** Parsed OData metadata document. */
final readonly class ODataMetadata
{
    /**
     * @param list<EdmEntityType> $entityTypes
     * @param list<EdmEntitySet> $entitySets
     * @param list<EdmAction> $actions
     * @param list<EdmEnumType> $enumTypes
     */
    public function __construct(
        public string $namespace,
        public array $entityTypes,
        public array $entitySets,
        public array $actions,
        public array $enumTypes,
        public string $raw,
        public ?string $productVersion = null,
        public ?string $serverVersion = null,
    ) {}
}
