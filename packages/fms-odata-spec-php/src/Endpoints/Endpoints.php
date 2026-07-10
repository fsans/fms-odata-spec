<?php

declare(strict_types=1);

namespace FmsOData\Spec\Endpoints;

use FmsOData\Spec\Versions\FMVersionMajor;

/**
 * Static facade for all OData API endpoints.
 *
 * Port every descriptor exactly from the TS/Python packages, including the
 * 2.0.1 corrections for DDL (slash-separated paths) and webhooks (Delete
 * rather than Remove, optional IDs in paths).
 */
final class Endpoints
{
    /** Ordered list of concrete (non-future) versions, oldest first. */
    private const VERSION_ORDER = ['20', '21', '22', '26'];

    /** @return list<FMEndpoint> */
    public static function all(): array
    {
        return [
            // Discovery & metadata
            new FMEndpoint('getDatabases', HttpMethod::GET, '/', 'List all hosted databases', FMVersionMajor::V20, EndpointCategory::DISCOVERY),
            new FMEndpoint('getTables', HttpMethod::GET, '/{database}', 'List tables in a database', FMVersionMajor::V20, EndpointCategory::DISCOVERY),
            new FMEndpoint('getSystemTable', HttpMethod::GET, '/{database}/{systemTable}', 'Get system table values', FMVersionMajor::V20, EndpointCategory::DISCOVERY),
            new FMEndpoint('getMetadata', HttpMethod::GET, '/{database}/$metadata', 'Get CSDL/EDMX metadata', FMVersionMajor::V20, EndpointCategory::METADATA),

            // Query
            new FMEndpoint('getRecords', HttpMethod::GET, '/{database}/{table}', 'Query records from a table', FMVersionMajor::V20, EndpointCategory::QUERY, queryOptions: ['$filter', '$select', '$orderby', '$top', '$skip', '$expand', '$count', '$apply']),
            new FMEndpoint('getRecord', HttpMethod::GET, '/{database}/{table}({key})', 'Get a single record', FMVersionMajor::V20, EndpointCategory::QUERY, queryOptions: ['$select']),
            new FMEndpoint('getFieldValue', HttpMethod::GET, '/{database}/{table}({key})/{field}', 'Get a single field value', FMVersionMajor::V20, EndpointCategory::QUERY),
            new FMEndpoint('getBinaryFieldValue', HttpMethod::GET, '/{database}/{table}({key})/{field}/$value', 'Get binary value of a container field', FMVersionMajor::V20, EndpointCategory::QUERY),
            new FMEndpoint('navigateRelated', HttpMethod::GET, '/{database}/{table}({key})/{relatedTable}', 'Navigate to related table records', FMVersionMajor::V20, EndpointCategory::QUERY, queryOptions: ['$expand', '$select']),

            // CRUD
            new FMEndpoint('createRecord', HttpMethod::POST, '/{database}/{table}', 'Create a new record', FMVersionMajor::V20, EndpointCategory::CRUD, alsoMethods: [HttpMethod::PUT], contentType: 'application/json'),
            new FMEndpoint('updateRecord', HttpMethod::PATCH, '/{database}/{table}({key})', 'Update a record', FMVersionMajor::V20, EndpointCategory::CRUD, alsoMethods: [HttpMethod::PUT], contentType: 'application/json'),
            new FMEndpoint('deleteRecord', HttpMethod::DELETE, '/{database}/{table}({key})', 'Delete a record', FMVersionMajor::V20, EndpointCategory::CRUD),
            new FMEndpoint('updateRecordRef', HttpMethod::POST, '/{database}/{table}({key})/{relatedTable}/$ref', 'Add/replace a record reference', FMVersionMajor::V20, EndpointCategory::CRUD, alsoMethods: [HttpMethod::PATCH, HttpMethod::PUT, HttpMethod::DELETE], contentType: 'application/json'),

            // Batch
            new FMEndpoint('batch', HttpMethod::POST, '/{database}/$batch', 'Perform batch operations', FMVersionMajor::V20, EndpointCategory::BATCH, contentType: 'multipart/mixed'),

            // Scripts
            new FMEndpoint('runScript', HttpMethod::POST, '/{database}/Script.{scriptName}', 'Run a FileMaker script by name', FMVersionMajor::V20, EndpointCategory::SCRIPTS, contentType: 'application/json'),
            new FMEndpoint('runScriptById', HttpMethod::POST, '/{database}/Script.FMSID:{scriptId}', 'Run a FileMaker script by FMSID', FMVersionMajor::V21, EndpointCategory::SCRIPTS, contentType: 'application/json'),

            // Containers
            new FMEndpoint('updateContainerBinary', HttpMethod::PATCH, '/{database}/{table}({key})/{containerField}', 'Update a container field with binary data', FMVersionMajor::V20, EndpointCategory::CONTAINERS),
            new FMEndpoint('updateContainerBase64', HttpMethod::PATCH, '/{database}/{table}({key})', 'Update container fields with base64-encoded data', FMVersionMajor::V20, EndpointCategory::CONTAINERS, alsoMethods: [HttpMethod::PUT], contentType: 'application/json'),

            // Schema
            new FMEndpoint('createTable', HttpMethod::POST, '/{database}/FileMaker_Tables', 'Create a new table', FMVersionMajor::V20, EndpointCategory::SCHEMA, contentType: 'application/json'),
            new FMEndpoint('addFields', HttpMethod::PATCH, '/{database}/FileMaker_Tables/{tableName}', 'Add fields to a table', FMVersionMajor::V20, EndpointCategory::SCHEMA, alsoMethods: [HttpMethod::PUT], contentType: 'application/json'),
            new FMEndpoint('deleteTable', HttpMethod::DELETE, '/{database}/FileMaker_Tables/{tableName}', 'Delete a table', FMVersionMajor::V20, EndpointCategory::SCHEMA),
            new FMEndpoint('deleteField', HttpMethod::DELETE, '/{database}/FileMaker_Tables/{tableName}/{fieldName}', 'Delete a field from a table', FMVersionMajor::V20, EndpointCategory::SCHEMA),
            new FMEndpoint('createIndex', HttpMethod::POST, '/{database}/FileMaker_Indexes/{tableName}', 'Create a field index (body: { indexName: <fieldName> })', FMVersionMajor::V20, EndpointCategory::SCHEMA, contentType: 'application/json'),
            new FMEndpoint('deleteIndex', HttpMethod::DELETE, '/{database}/FileMaker_Indexes/{tableName}/{fieldName}', 'Delete an index', FMVersionMajor::V20, EndpointCategory::SCHEMA),

            // Webhooks
            new FMEndpoint('createWebhook', HttpMethod::POST, '/{database}/Webhook.Add', 'Create a webhook', FMVersionMajor::V22, EndpointCategory::WEBHOOKS, contentType: 'application/json'),
            new FMEndpoint('deleteWebhook', HttpMethod::POST, '/{database}/Webhook.Delete({webhookId})', 'Delete a webhook by id', FMVersionMajor::V22, EndpointCategory::WEBHOOKS),
            new FMEndpoint('getWebhook', HttpMethod::GET, '/{database}/Webhook.Get({webhookId})', 'Get specified webhook data by id', FMVersionMajor::V22, EndpointCategory::WEBHOOKS),
            new FMEndpoint('getAllWebhooks', HttpMethod::GET, '/{database}/Webhook.GetAll', 'Get all webhooks', FMVersionMajor::V22, EndpointCategory::WEBHOOKS),
            new FMEndpoint('invokeWebhook', HttpMethod::POST, '/{database}/Webhook.Invoke({webhookId})', 'Manually invoke a webhook (body: { rowIDs: [...] })', FMVersionMajor::V22, EndpointCategory::WEBHOOKS, contentType: 'application/json'),
        ];
    }

    /** Find an endpoint by ID. Returns null if not found. */
    public static function get(string $id): ?FMEndpoint
    {
        foreach (self::all() as $endpoint) {
            if ($endpoint->id === $id) {
                return $endpoint;
            }
        }

        return null;
    }

    /**
     * Get all endpoints available in a given version.
     *
     * @return list<FMEndpoint>
     */
    public static function forVersion(FMVersionMajor $version): array
    {
        if ($version === FMVersionMajor::FUTURE) {
            return self::all();
        }

        $idx = \array_search($version->value, self::VERSION_ORDER, true);
        if ($idx === false) {
            return self::all();
        }

        return \array_values(\array_filter(
            self::all(),
            fn (FMEndpoint $e): bool => \array_search($e->minVersion->value, self::VERSION_ORDER, true) !== false
                && \array_search($e->minVersion->value, self::VERSION_ORDER, true) <= $idx,
        ));
    }
}
