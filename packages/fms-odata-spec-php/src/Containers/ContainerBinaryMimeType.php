<?php

declare(strict_types=1);

namespace FmsOData\Spec\Containers;

/** MIME types supported for binary container upload. */
enum ContainerBinaryMimeType: string
{
    case PNG = 'image/png';
    case JPEG = 'image/jpeg';
    case GIF = 'image/gif';
    case TIFF = 'image/tiff';
    case PDF = 'application/pdf';

    /** @return list<ContainerBinaryMimeType> */
    public static function all(): array
    {
        return [
            self::PNG,
            self::JPEG,
            self::GIF,
            self::TIFF,
            self::PDF,
        ];
    }
}
