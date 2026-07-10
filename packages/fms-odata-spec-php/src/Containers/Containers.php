<?php

declare(strict_types=1);

namespace FmsOData\Spec\Containers;

/**
 * Static facade for container field helpers.
 *
 * Mirrors sniffContainerMime(), buildContentDisposition(), and toBase64()
 * from the TS/Python packages.
 */
final class Containers
{
    /** @var non-empty-string ASCII-only pattern for filenames. */
    private const ASCII_RE = '/^[\x20-\x7E]+$/';

    /**
     * Sniff the MIME type from file magic bytes.
     *
     * Returns one of the supported binary types, or null if unrecognized.
     *
     * @param string $bytesData Binary data (PHP binary string).
     */
    public static function sniffMime(string $bytesData): ?ContainerBinaryMimeType
    {
        if (\strlen($bytesData) < 4) {
            return null;
        }
        $b = $bytesData;
        // PNG: 89 50 4E 47
        if (\ord($b[0]) === 0x89 && \ord($b[1]) === 0x50 && \ord($b[2]) === 0x4E && \ord($b[3]) === 0x47) {
            return ContainerBinaryMimeType::PNG;
        }
        // JPEG: FF D8 FF
        if (\ord($b[0]) === 0xFF && \ord($b[1]) === 0xD8 && \ord($b[2]) === 0xFF) {
            return ContainerBinaryMimeType::JPEG;
        }
        // GIF: 47 49 46 38
        if (\ord($b[0]) === 0x47 && \ord($b[1]) === 0x49 && \ord($b[2]) === 0x46 && \ord($b[3]) === 0x38) {
            return ContainerBinaryMimeType::GIF;
        }
        // TIFF: 49 49 2A 00 or 4D 4D 00 2A
        if ((\ord($b[0]) === 0x49 && \ord($b[1]) === 0x49 && \ord($b[2]) === 0x2A && \ord($b[3]) === 0x00) ||
            (\ord($b[0]) === 0x4D && \ord($b[1]) === 0x4D && \ord($b[2]) === 0x00 && \ord($b[3]) === 0x2A)) {
            return ContainerBinaryMimeType::TIFF;
        }
        // PDF: 25 50 44 46
        if (\ord($b[0]) === 0x25 && \ord($b[1]) === 0x50 && \ord($b[2]) === 0x44 && \ord($b[3]) === 0x46) {
            return ContainerBinaryMimeType::PDF;
        }

        return null;
    }

    /**
     * Build a Content-Disposition header value for container upload.
     *
     * Uses unquoted form for ASCII, RFC 5987 for non-ASCII.
     */
    public static function buildContentDisposition(string $filename): string
    {
        if (\preg_match(self::ASCII_RE, $filename)) {
            return 'inline; filename=' . $filename;
        }

        // RFC 5987 for non-ASCII
        $encoded = \rawurlencode($filename);

        return "inline; filename*=UTF-8''" . $encoded;
    }

    /**
     * Convert binary data to a base64 string.
     *
     * @param string $data Binary data (PHP binary string).
     */
    public static function toBase64(string $data): string
    {
        return \base64_encode($data);
    }
}
