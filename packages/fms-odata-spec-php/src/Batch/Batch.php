<?php

declare(strict_types=1);

namespace FmsOData\Spec\Batch;

/**
 * Static facade for batch helpers.
 *
 * Mirrors generateBoundary() from the TS/Python packages. Boundaries are
 * generated with random_bytes() and hexadecimal encoding; no UUID dependency.
 */
final class Batch
{
    /**
     * Generate a unique boundary string for multipart MIME.
     *
     * Uses random_bytes() for cryptographic-quality randomness.
     */
    public static function generateBoundary(string $prefix = 'batch'): string
    {
        $bytes = \random_bytes(16);
        $hex = \bin2hex($bytes);

        return $prefix . '_' . $hex;
    }
}
