<?php

declare(strict_types=1);

namespace FmsOData\Spec\Tests;

use FmsOData\Spec\Package;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Reflection-based public API test that verifies all expected public
 * classes/enums autoload, verifies Package::VERSION, and catches omissions
 * from the TS/Python surface.
 */
final class PublicApiTest extends TestCase
{
    public function testPackageVersion(): void
    {
        self::assertSame('2.0.1', Package::VERSION);
    }

    #[DataProvider('publicClassProvider')]
    public function testAllPublicClassesAutoload(string $fqcn): void
    {
        self::assertTrue(
            \class_exists($fqcn) || \interface_exists($fqcn) || \enum_exists($fqcn),
            \sprintf('Failed to autoload expected public type: %s', $fqcn),
        );
    }

    /**
     * @return \Generator<string, array{string}>
     */
    public static function publicClassProvider(): \Generator
    {
        $classes = [
            // Package
            Package::class,

            // Versions
            \FmsOData\Spec\Versions\FMVersionMajor::class,
            \FmsOData\Spec\Versions\FMVersionStatus::class,
            \FmsOData\Spec\Versions\ODataProtocolVersion::class,
            \FmsOData\Spec\Versions\FMFeatureFlags::class,
            \FmsOData\Spec\Versions\FMQueryOptionFlags::class,
            \FmsOData\Spec\Versions\FMVersionInfo::class,
            \FmsOData\Spec\Versions\Versions::class,

            // Auth
            \FmsOData\Spec\Auth\FMAuthScheme::class,
            \FmsOData\Spec\Auth\FMBasicAuthConfig::class,
            \FmsOData\Spec\Auth\FMOAuthAuthConfig::class,
            \FmsOData\Spec\Auth\FMAuthHeaders::class,
            \FmsOData\Spec\Auth\Auth::class,

            // Endpoints
            \FmsOData\Spec\Endpoints\HttpMethod::class,
            \FmsOData\Spec\Endpoints\EndpointCategory::class,
            \FmsOData\Spec\Endpoints\FMEndpoint::class,
            \FmsOData\Spec\Endpoints\Endpoints::class,

            // Query
            \FmsOData\Spec\Query\QueryOption::class,
            \FmsOData\Spec\Query\UnsupportedQueryOption::class,
            \FmsOData\Spec\Query\FilterComparisonOp::class,
            \FmsOData\Spec\Query\FilterLogicalOp::class,
            \FmsOData\Spec\Query\FilterStringFunction::class,
            \FmsOData\Spec\Query\FilterDateTimeFunction::class,
            \FmsOData\Spec\Query\FilterNumericFunction::class,
            \FmsOData\Spec\Query\UnsupportedFilterFunction::class,
            \FmsOData\Spec\Query\SortDirection::class,
            \FmsOData\Spec\Query\AggregateFunction::class,
            \FmsOData\Spec\Query\OrderByClause::class,
            \FmsOData\Spec\Query\AggregateExpression::class,
            \FmsOData\Spec\Query\GroupByExpression::class,
            \FmsOData\Spec\Query\ApplyTransformation::class,
            \FmsOData\Spec\Query\AggregateTransformation::class,
            \FmsOData\Spec\Query\GroupByTransformation::class,
            \FmsOData\Spec\Query\QueryParams::class,
            \FmsOData\Spec\Query\ODataCollection::class,
            \FmsOData\Spec\Query\ODataEntity::class,
            \FmsOData\Spec\Query\QueryResult::class,
            \FmsOData\Spec\Query\QueryLiterals::class,

            // Metadata
            \FmsOData\Spec\Metadata\FMBooleanAnnotations::class,
            \FmsOData\Spec\Metadata\FMValueAnnotations::class,
            \FmsOData\Spec\Metadata\FMAnnotations::class,
            \FmsOData\Spec\Metadata\EdmProperty::class,
            \FmsOData\Spec\Metadata\EdmEntityType::class,
            \FmsOData\Spec\Metadata\EdmEntitySet::class,
            \FmsOData\Spec\Metadata\EdmAction::class,
            \FmsOData\Spec\Metadata\EdmEnumMember::class,
            \FmsOData\Spec\Metadata\EdmEnumType::class,
            \FmsOData\Spec\Metadata\ODataMetadata::class,
            \FmsOData\Spec\Metadata\ImmutableIdType::class,
            \FmsOData\Spec\Metadata\FMServerVersion::class,
            \FmsOData\Spec\Metadata\Metadata::class,

            // Scripts
            \FmsOData\Spec\Scripts\ScriptScope::class,
            \FmsOData\Spec\Scripts\ScriptOptions::class,
            \FmsOData\Spec\Scripts\ScriptIdentifier::class,
            \FmsOData\Spec\Scripts\ScriptNameId::class,
            \FmsOData\Spec\Scripts\ScriptFmsidId::class,
            \FmsOData\Spec\Scripts\ScriptResultInner::class,
            \FmsOData\Spec\Scripts\ScriptResultEnvelope::class,
            \FmsOData\Spec\Scripts\ScriptResult::class,
            \FmsOData\Spec\Scripts\ScriptDescriptor::class,
            \FmsOData\Spec\Scripts\Scripts::class,

            // Containers
            \FmsOData\Spec\Containers\ContainerBinaryMimeType::class,
            \FmsOData\Spec\Containers\ContainerEncoding::class,
            \FmsOData\Spec\Containers\ContainerUploadInput::class,
            \FmsOData\Spec\Containers\ContainerDownload::class,
            \FmsOData\Spec\Containers\FMContainerAnnotations::class,
            \FmsOData\Spec\Containers\Containers::class,

            // Batch
            \FmsOData\Spec\Batch\BatchOpType::class,
            \FmsOData\Spec\Batch\BatchOperation::class,
            \FmsOData\Spec\Batch\Changeset::class,
            \FmsOData\Spec\Batch\BatchRequest::class,
            \FmsOData\Spec\Batch\BatchOpResult::class,
            \FmsOData\Spec\Batch\BatchResult::class,
            \FmsOData\Spec\Batch\BatchHandle::class,
            \FmsOData\Spec\Batch\Batch::class,

            // Webhooks
            \FmsOData\Spec\Webhooks\WebhookOperation::class,
            \FmsOData\Spec\Webhooks\WebhookCreateParams::class,
            \FmsOData\Spec\Webhooks\WebhookData::class,
            \FmsOData\Spec\Webhooks\WebhookInvokeParams::class,
            \FmsOData\Spec\Webhooks\WebhookCreateResult::class,
            \FmsOData\Spec\Webhooks\Webhooks::class,

            // Schema
            \FmsOData\Spec\Schema\FMFieldType::class,
            \FmsOData\Spec\Schema\FMFieldDefinition::class,
            \FmsOData\Spec\Schema\CreateTableParams::class,
            \FmsOData\Spec\Schema\AddFieldsParams::class,
            \FmsOData\Spec\Schema\ParsedFieldType::class,
            \FmsOData\Spec\Schema\Schema::class,

            // Errors
            \FmsOData\Spec\Errors\ODataErrorDetail::class,
            \FmsOData\Spec\Errors\ODataErrorInnerError::class,
            \FmsOData\Spec\Errors\ODataErrorInner::class,
            \FmsOData\Spec\Errors\ODataErrorBody::class,
            \FmsOData\Spec\Errors\RequestRef::class,
            \FmsOData\Spec\Errors\FMODataError::class,
            \FmsOData\Spec\Errors\FMScriptError::class,
            \FmsOData\Spec\Errors\FMAuthError::class,
            \FmsOData\Spec\Errors\FMNotFoundError::class,
            \FmsOData\Spec\Errors\FMValidationError::class,
            \FmsOData\Spec\Errors\Errors::class,
        ];

        foreach ($classes as $fqcn) {
            yield $fqcn => [$fqcn];
        }
    }
}
