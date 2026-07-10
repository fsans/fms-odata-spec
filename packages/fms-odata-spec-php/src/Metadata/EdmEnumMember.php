<?php

declare(strict_types=1);

namespace FmsOData\Spec\Metadata;

/** A member of an Edm enum type (FileMaker value list entry). */
final readonly class EdmEnumMember
{
    public function __construct(
        public string $name,
        public string|int $value,
    ) {}
}
