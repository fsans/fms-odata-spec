<?php

declare(strict_types=1);

namespace FmsOData\Spec\Schema;

/** Result of parseFieldType(). */
final readonly class ParsedFieldType
{
    public function __construct(
        public string $baseType,
        public ?int $length = null,
        public ?int $repetitions = null,
    ) {}
}
