<?php

declare(strict_types=1);

namespace FmsOData\Spec\Versions;

/**
 * Version matrix, names, constants, and feature/query-option helpers.
 *
 * This is a final static facade — no instances are created. Because PHP
 * constants cannot hold DTO instances, the matrix is exposed through the
 * deterministic static method {@see Versions::matrix()}.
 */
final class Versions
{
    /** OData protocol version implemented by FileMaker (latest). */
    public const ODATA_PROTOCOL_VERSION = '4.01';

    /** OData conformance level. */
    public const ODATA_CONFORMANCE_LEVEL = 'intermediate';

    /** Default server-driven page size (records per page). */
    public const DEFAULT_PAGE_SIZE = 10000;

    /** Ordered list of concrete (non-future) versions, oldest first. */
    private const VERSION_ORDER = ['20', '21', '22', '26'];

    /**
     * Human-readable version names keyed by major version string.
     *
     * @return array<string, string>
     */
    /**
     * @return array<int|string, string>
     */
    public static function names(): array
    {
        return [
            '20' => 'Claris FileMaker 2023',
            '21' => 'Claris FileMaker 2024',
            '22' => 'Claris FileMaker 2025',
            '26' => 'Claris FileMaker 2026',
            'future' => 'Future / next',
        ];
    }

    /**
     * The full version feature/query-option matrix keyed by major version string.
     *
     * @return array<int|string, FMVersionInfo>
     */
    public static function matrix(): array
    {
        return [
            '20' => new FMVersionInfo(
                major: FMVersionMajor::V20,
                name: 'Claris FileMaker 2023',
                releaseYear: 2023,
                internalVersion: '20.x',
                status: FMVersionStatus::SUPPORTED,
                odataProtocolVersion: ODataProtocolVersion::V4_0,
                features: new FMFeatureFlags(
                    serviceDocument: true, metadata: true, databaseListing: true, tableListing: true,
                    recordCrud: true, recordReferences: true, crossJoin: true, batch: true,
                    scripts: true, scriptsByFmsid: false, scriptListing: false,
                    containerBinaryUpload: true, containerBase64Upload: true, containerDownload: true,
                    schemaModification: true, webhooks: false, webhookQueryHeaders: false,
                    applyAggregation: false, typeCasting: false, parameterizedFilters: false,
                    simplifiedQuerySyntax: false, nestedQueries: false, batchPreferenceInheritance: false,
                    metadataFiltering: false, immutableIdUrls: false,
                    fmComment: false, aiAnnotation: false, computedAnnotation: false,
                    serverVersionAnnotation: false, enrichedFmComment: false,
                    authBasic: true, authOAuth: false,
                ),
                queryOptions: new FMQueryOptionFlags(
                    filter: true, select: true, orderby: true, top: true, skip: true,
                    expand: true, count: true, apply: false, search: false, compute: false,
                ),
            ),
            '21' => new FMVersionInfo(
                major: FMVersionMajor::V21,
                name: 'Claris FileMaker 2024',
                releaseYear: 2024,
                internalVersion: '21.x',
                status: FMVersionStatus::SUPPORTED,
                odataProtocolVersion: ODataProtocolVersion::V4_01,
                features: new FMFeatureFlags(
                    serviceDocument: true, metadata: true, databaseListing: true, tableListing: true,
                    recordCrud: true, recordReferences: true, crossJoin: true, batch: true,
                    scripts: true, scriptsByFmsid: true, scriptListing: false,
                    containerBinaryUpload: true, containerBase64Upload: true, containerDownload: true,
                    schemaModification: true, webhooks: false, webhookQueryHeaders: false,
                    applyAggregation: false, typeCasting: true, parameterizedFilters: true,
                    simplifiedQuerySyntax: true, nestedQueries: true, batchPreferenceInheritance: true,
                    metadataFiltering: false, immutableIdUrls: false,
                    fmComment: true, aiAnnotation: true, computedAnnotation: false,
                    serverVersionAnnotation: false, enrichedFmComment: false,
                    authBasic: true, authOAuth: true,
                ),
                queryOptions: new FMQueryOptionFlags(
                    filter: true, select: true, orderby: true, top: true, skip: true,
                    expand: true, count: true, apply: false, search: false, compute: false,
                ),
            ),
            '22' => new FMVersionInfo(
                major: FMVersionMajor::V22,
                name: 'Claris FileMaker 2025',
                releaseYear: 2025,
                internalVersion: '22.x',
                status: FMVersionStatus::SUPPORTED,
                odataProtocolVersion: ODataProtocolVersion::V4_01,
                features: new FMFeatureFlags(
                    serviceDocument: true, metadata: true, databaseListing: true, tableListing: true,
                    recordCrud: true, recordReferences: true, crossJoin: true, batch: true,
                    scripts: true, scriptsByFmsid: true, scriptListing: false,
                    containerBinaryUpload: true, containerBase64Upload: true, containerDownload: true,
                    schemaModification: true, webhooks: true, webhookQueryHeaders: true,
                    applyAggregation: true, typeCasting: true, parameterizedFilters: true,
                    simplifiedQuerySyntax: true, nestedQueries: true, batchPreferenceInheritance: true,
                    metadataFiltering: true, immutableIdUrls: false,
                    fmComment: true, aiAnnotation: true, computedAnnotation: false,
                    serverVersionAnnotation: false, enrichedFmComment: false,
                    authBasic: true, authOAuth: true,
                ),
                queryOptions: new FMQueryOptionFlags(
                    filter: true, select: true, orderby: true, top: true, skip: true,
                    expand: true, count: true, apply: true, search: false, compute: false,
                ),
            ),
            '26' => new FMVersionInfo(
                major: FMVersionMajor::V26,
                name: 'Claris FileMaker 2026',
                releaseYear: 2026,
                internalVersion: '26.x',
                status: FMVersionStatus::CURRENT,
                odataProtocolVersion: ODataProtocolVersion::V4_01,
                features: new FMFeatureFlags(
                    serviceDocument: true, metadata: true, databaseListing: true, tableListing: true,
                    recordCrud: true, recordReferences: true, crossJoin: true, batch: true,
                    scripts: true, scriptsByFmsid: true, scriptListing: true,
                    containerBinaryUpload: true, containerBase64Upload: true, containerDownload: true,
                    schemaModification: true, webhooks: true, webhookQueryHeaders: true,
                    applyAggregation: true, typeCasting: true, parameterizedFilters: true,
                    simplifiedQuerySyntax: true, nestedQueries: true, batchPreferenceInheritance: true,
                    metadataFiltering: true, immutableIdUrls: true,
                    fmComment: true, aiAnnotation: true, computedAnnotation: true,
                    serverVersionAnnotation: true, enrichedFmComment: true,
                    authBasic: true, authOAuth: true,
                ),
                queryOptions: new FMQueryOptionFlags(
                    filter: true, select: true, orderby: true, top: true, skip: true,
                    expand: true, count: true, apply: true, search: false, compute: false,
                ),
            ),
            'future' => new FMVersionInfo(
                major: FMVersionMajor::FUTURE,
                name: 'Future / next',
                releaseYear: null,
                internalVersion: 'unknown',
                status: FMVersionStatus::FUTURE,
                odataProtocolVersion: ODataProtocolVersion::V4_01,
                features: new FMFeatureFlags(
                    serviceDocument: true, metadata: true, databaseListing: true, tableListing: true,
                    recordCrud: true, recordReferences: true, crossJoin: true, batch: true,
                    scripts: true, scriptsByFmsid: true, scriptListing: true,
                    containerBinaryUpload: true, containerBase64Upload: true, containerDownload: true,
                    schemaModification: true, webhooks: true, webhookQueryHeaders: true,
                    applyAggregation: true, typeCasting: true, parameterizedFilters: true,
                    simplifiedQuerySyntax: true, nestedQueries: true, batchPreferenceInheritance: true,
                    metadataFiltering: true, immutableIdUrls: true,
                    fmComment: true, aiAnnotation: true, computedAnnotation: true,
                    serverVersionAnnotation: true, enrichedFmComment: true,
                    authBasic: true, authOAuth: true,
                ),
                queryOptions: new FMQueryOptionFlags(
                    filter: true, select: true, orderby: true, top: true, skip: true,
                    expand: true, count: true, apply: true, search: false, compute: false,
                ),
            ),
        ];
    }

    /**
     * Check if a feature is available in a given version.
     *
     * @param string $version Major version string (e.g. "20", "26", "future")
     * @param string $feature  camelCase feature name (e.g. "webhooks", "scriptsByFmsid")
     */
    public static function hasFeature(string $version, string $feature): bool
    {
        $matrix = self::matrix();
        $info = $matrix[$version] ?? null;
        if ($info === null) {
            return false;
        }

        return $info->features->has($feature);
    }

    /**
     * Check if a query option is available in a given version.
     *
     * @param string $version Major version string (e.g. "20", "26")
     * @param string $option   camelCase option name (e.g. "filter", "apply")
     */
    public static function hasQueryOption(string $version, string $option): bool
    {
        $matrix = self::matrix();
        $info = $matrix[$version] ?? null;
        if ($info === null) {
            return false;
        }

        return $info->queryOptions->has($option);
    }

    /**
     * Get the minimum version that supports a given feature, or null.
     *
     * @param string $feature camelCase feature name
     */
    public static function minVersionForFeature(string $feature): ?string
    {
        $matrix = self::matrix();
        foreach (self::VERSION_ORDER as $v) {
            $info = $matrix[$v] ?? null;
            if ($info !== null && $info->features->has($feature)) {
                return $v;
            }
        }

        return null;
    }
}
