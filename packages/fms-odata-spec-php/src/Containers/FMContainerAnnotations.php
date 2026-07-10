<?php

declare(strict_types=1);

namespace FmsOData\Spec\Containers;

/**
 * FileMaker-specific container annotations for base64 JSON body.
 *
 * The TS interface uses dotted/@-prefixed keys (@com.filemaker.odata.Filename)
 * which are not valid PHP identifiers. The properties are therefore $filename,
 * $contentType, $data; use {@see FMContainerAnnotations::toODataArray()} to
 * emit the original wire keys for JSON serialization.
 */
final readonly class FMContainerAnnotations
{
    public function __construct(
        public string $filename,
        public string $contentType,
        /** base64-encoded payload. */
        public string $data,
    ) {}

    /** @return array<string, string> */
    public function toODataArray(): array
    {
        return [
            '@com.filemaker.odata.Filename' => $this->filename,
            '@com.filemaker.odata.ContentType' => $this->contentType,
            'data' => $this->data,
        ];
    }
}
