<?php

declare(strict_types=1);

namespace FmsOData\Spec\Containers;

/** Container download result. */
final readonly class ContainerDownload
{
    public function __construct(
        public string $data,
        public string $contentType,
        public ?string $filename = null,
    ) {}
}
