<?php

declare(strict_types=1);

namespace FmsOData\Spec\Containers;

/** Input for uploading container data. */
final readonly class ContainerUploadInput
{
    /**
     * @param string $data Binary data (PHP binary string).
     * @param ?string $contentType MIME type. Auto-detected from magic bytes if omitted for binary mode.
     * @param ?string $filename Filename to store in the container.
     * @param ?ContainerEncoding $encoding Encoding mode. Default: binary.
     */
    public function __construct(
        public string $data,
        public ?string $contentType = null,
        public ?string $filename = null,
        public ?ContainerEncoding $encoding = null,
    ) {}
}
