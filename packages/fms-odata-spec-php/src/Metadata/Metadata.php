<?php

declare(strict_types=1);

namespace FmsOData\Spec\Metadata;

/**
 * Static facade for metadata constants and $metadata version-detection helpers.
 *
 * System fields and tables are exposed as constants. The four version-detection
 * strategies are ported exactly from the TS/Python packages, using a
 * dependency-free regex approach (no ext-dom required).
 */
final class Metadata
{
    /** FileMaker system fields. */
    public const SYSTEM_FIELDS = [
        'ROWID' => 'ROWID',
        'ROWMODID' => 'ROWMODID',
    ];

    /** FileMaker system tables. */
    public const SYSTEM_TABLES = [
        'TABLES' => 'FileMaker_Tables',
        'FIELDS' => 'FileMaker_Fields',
        'INDEXES' => 'FileMaker_Indexes',
    ];

    /** @var non-empty-string Regex for a three-part semver. */
    private const VERSION_RE = '/(\d+)\.(\d+)\.(\d+)/';

    /**
     * Extract a three-part semver from a raw version string.
     *
     * Handles strings that may contain a build number (e.g. "21.1.2.500" ->
     * major=21, minor=1, patch=2). Returns null if the string doesn't contain
     * a parseable version.
     */
    public static function parseVersionString(string $raw): ?FMServerVersion
    {
        $trimmed = \trim($raw);
        if ($trimmed === '') {
            return null;
        }
        if (!\preg_match(self::VERSION_RE, $trimmed, $m)) {
            return null;
        }

        return new FMServerVersion(
            major: (int) $m[1],
            minor: (int) $m[2],
            patch: (int) $m[3],
            raw: $trimmed,
        );
    }

    /**
     * Parse the FileMaker Server version from an OData $metadata XML string.
     *
     * Detection reads the $metadata XML using 4 strategies in priority order:
     *
     * 1a. <Annotation Term="Org.OData.Core.V1.ProductVersion" String="x.x.x.build"/>
     * 1b. Same as 1a but with reversed attribute order (String before Term)
     * 1c. <Annotation Term="ServerVersion" String="OData Engine 26.0.1"/> (FM 26+)
     * 1d. Same as 1c but with reversed attribute order
     * 2.  Generic fallback: any annotation whose term contains "Version" and whose
     *     String value contains a version with major >= 17 (avoids false positives
     *     from OData spec "4.0")
     *
     * Returns null if no strategy yields a parseable version.
     */
    public static function parseServerVersion(?string $metadataXml): ?FMServerVersion
    {
        if ($metadataXml === null || $metadataXml === '') {
            return null;
        }

        // Strategy 1a: canonical ProductVersion annotation (String attribute after Term)
        if (\preg_match(
            '/Term\s*=\s*["\']Org\.OData\.Core\.V1\.ProductVersion["\'][^>]*?String\s*=\s*["\']([^"\']+)["\']/',
            $metadataXml,
            $m,
        )) {
            $v = self::parseVersionString($m[1]);
            if ($v !== null) {
                return $v;
            }
        }

        // Strategy 1b: reversed attribute order (String before Term)
        if (\preg_match(
            '/String\s*=\s*["\']([^"\']+)["\'][^>]*?Term\s*=\s*["\']Org\.OData\.Core\.V1\.ProductVersion["\']/',
            $metadataXml,
            $m,
        )) {
            $v = self::parseVersionString($m[1]);
            if ($v !== null) {
                return $v;
            }
        }

        // Strategy 1c: ServerVersion annotation (FM 26+, e.g. "OData Engine 26.0.1")
        if (\preg_match(
            '/Term\s*=\s*["\']ServerVersion["\'][^>]*?String\s*=\s*["\']([^"\']+)["\']/i',
            $metadataXml,
            $m,
        )) {
            $v = self::parseVersionString($m[1]);
            if ($v !== null && $v->major >= 17) {
                return $v;
            }
        }

        // Strategy 1d: reversed attribute order for ServerVersion
        if (\preg_match(
            '/String\s*=\s*["\']([^"\']+)["\'][^>]*?Term\s*=\s*["\']ServerVersion["\']/i',
            $metadataXml,
            $m,
        )) {
            $v = self::parseVersionString($m[1]);
            if ($v !== null && $v->major >= 17) {
                return $v;
            }
        }

        // Strategy 2: generic fallback — any annotation with "Version" in the term
        // and a String value containing a version with major >= 17
        if (\preg_match(
            '/Term\s*=\s*["\'][^"\']*Version[^"\']*["\'][^>]*?String\s*=\s*["\'][^"\']*?(\d{2,}\.\d+\.\d+(?:\.\d+)?)[^"\']*?["\']/i',
            $metadataXml,
            $m,
        )) {
            $v = self::parseVersionString($m[1]);
            if ($v !== null && $v->major >= 17) {
                return $v;
            }
        }

        return null;
    }

    /**
     * Extract the FileMaker Server major version string from a product version
     * string. Convenience wrapper around parseVersionString().
     * Returns null if the version cannot be determined.
     */
    public static function extractMajorVersion(?string $productVersion): ?string
    {
        if ($productVersion === null || $productVersion === '') {
            return null;
        }
        $v = self::parseVersionString($productVersion);

        return $v !== null ? (string) $v->major : null;
    }

    /**
     * Extract the FileMaker Server major version from a $metadata XML string.
     * Uses parseServerVersion() internally and returns just the major version
     * number as a string, or null if the version cannot be determined.
     */
    public static function extractMajorVersionFromMetadata(string $metadataXml): ?string
    {
        $v = self::parseServerVersion($metadataXml);

        return $v !== null ? (string) $v->major : null;
    }
}
