<?php

declare(strict_types=1);

namespace FmsOData\Spec\Metadata;

/** Parsed FileMaker Server version with major, minor, patch, and raw string. */
final readonly class FMServerVersion
{
    public function __construct(
        public int $major,
        public int $minor,
        public int $patch,
        /** Raw string exactly as found in the XML, e.g. "21.1.2.500" */
        public string $raw,
    ) {}
}
