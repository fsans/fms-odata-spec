<?php

declare(strict_types=1);

namespace FmsOData\Spec\Versions;

/**
 * Feature flags for a specific FileMaker Server version.
 *
 * Immutable readonly DTO mirroring FMFeatureFlags from the TS/Python packages.
 */
final readonly class FMFeatureFlags
{
    public function __construct(
        public bool $serviceDocument,
        public bool $metadata,
        public bool $databaseListing,
        public bool $tableListing,
        public bool $recordCrud,
        public bool $recordReferences,
        public bool $crossJoin,
        public bool $batch,
        public bool $scripts,
        public bool $scriptsByFmsid,
        public bool $scriptListing,
        public bool $containerBinaryUpload,
        public bool $containerBase64Upload,
        public bool $containerDownload,
        public bool $schemaModification,
        public bool $webhooks,
        public bool $webhookQueryHeaders,
        public bool $applyAggregation,
        public bool $typeCasting,
        public bool $parameterizedFilters,
        public bool $simplifiedQuerySyntax,
        public bool $nestedQueries,
        public bool $batchPreferenceInheritance,
        public bool $metadataFiltering,
        public bool $immutableIdUrls,
        public bool $fmComment,
        public bool $aiAnnotation,
        public bool $computedAnnotation,
        public bool $serverVersionAnnotation,
        public bool $enrichedFmComment,
        public bool $authBasic,
        public bool $authOAuth,
    ) {}

    /**
     * Check whether a feature is enabled by its camelCase property name.
     *
     * @param string $feature e.g. "webhooks", "scriptsByFmsid"
     */
    public function has(string $feature): bool
    {
        return \property_exists($this, $feature) && $this->{$feature} === true;
    }
}
