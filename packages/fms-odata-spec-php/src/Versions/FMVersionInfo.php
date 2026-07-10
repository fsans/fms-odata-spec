<?php

declare(strict_types=1);

namespace FmsOData\Spec\Versions;

/**
 * Complete version descriptor.
 *
 * Immutable readonly DTO mirroring FMVersionInfo from the TS/Python packages.
 */
final readonly class FMVersionInfo
{
    public function __construct(
        public FMVersionMajor $major,
        public string $name,
        public ?int $releaseYear,
        public string $internalVersion,
        public FMVersionStatus $status,
        public ODataProtocolVersion $odataProtocolVersion,
        public FMFeatureFlags $features,
        public FMQueryOptionFlags $queryOptions,
    ) {}
}
