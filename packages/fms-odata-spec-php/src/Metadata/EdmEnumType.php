<?php

declare(strict_types=1);

namespace FmsOData\Spec\Metadata;

/** Edm enum type (FileMaker value list). */
final readonly class EdmEnumType
{
    /** @param list<EdmEnumMember> $members */
    public function __construct(
        public string $name,
        public array $members = [],
    ) {}
}
