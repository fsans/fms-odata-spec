<?php

declare(strict_types=1);

namespace FmsOData\Spec\Tests\Endpoints;

use FmsOData\Spec\Endpoints\EndpointCategory;
use FmsOData\Spec\Endpoints\Endpoints;
use FmsOData\Spec\Endpoints\FMEndpoint;
use FmsOData\Spec\Endpoints\HttpMethod;
use FmsOData\Spec\Versions\FMVersionMajor;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Endpoints domain, ported from the Python test_endpoints.py.
 *
 * Verifies the endpoint table is complete, every descriptor has required
 * fields, version filtering behaves correctly, and the 2.0.1 corrections
 * for DDL (slash-separated paths) and webhooks (Delete rather than Remove)
 * are in place.
 */
final class EndpointsTest extends TestCase
{
    public function testEndpointsTableIsNonempty(): void
    {
        self::assertGreaterThanOrEqual(20, \count(Endpoints::all()));
    }

    public function testEveryEndpointHasRequiredFields(): void
    {
        foreach (Endpoints::all() as $endpoint) {
            self::assertNotSame('', $endpoint->id, 'Endpoint id must be non-empty');
            self::assertInstanceOf(HttpMethod::class, $endpoint->method);
            self::assertStringStartsWith('/', $endpoint->path, 'Endpoint path must start with /');
            self::assertNotSame('', $endpoint->description, 'Endpoint description must be non-empty');
            self::assertContains(
                $endpoint->minVersion->value,
                ['20', '21', '22', '26'],
                'Endpoint minVersion must be a known version',
            );
            self::assertInstanceOf(EndpointCategory::class, $endpoint->category);
        }
    }

    public function testGetEndpointReturnsMatch(): void
    {
        $endpoint = Endpoints::get('getRecords');

        self::assertNotNull($endpoint);
        self::assertSame(HttpMethod::GET, $endpoint->method);
        self::assertSame('/{database}/{table}', $endpoint->path);
    }

    public function testGetEndpointReturnsNullForUnknown(): void
    {
        self::assertNull(Endpoints::get('nope'));
    }

    public function testGetEndpointsForVersion20ExcludesWebhooks(): void
    {
        $ids = \array_map(
            static fn (mixed $e): string => $e instanceof FMEndpoint ? $e->id : '',
            Endpoints::forVersion(FMVersionMajor::V20),
        );

        self::assertContains('getRecords', $ids);
        self::assertNotContains('createWebhook', $ids);
        self::assertNotContains('runScriptById', $ids);
    }

    public function testGetEndpointsForVersion21IncludesScriptById(): void
    {
        $ids = \array_map(
            static fn (mixed $e): string => $e instanceof FMEndpoint ? $e->id : '',
            Endpoints::forVersion(FMVersionMajor::V21),
        );

        self::assertContains('runScriptById', $ids);
        self::assertNotContains('createWebhook', $ids);
    }

    public function testGetEndpointsForVersion26IncludesAll(): void
    {
        $ids = \array_map(
            static fn (mixed $e): string => $e instanceof FMEndpoint ? $e->id : '',
            Endpoints::forVersion(FMVersionMajor::V26),
        );

        self::assertContains('runScriptById', $ids);
        self::assertContains('createWebhook', $ids);
    }

    public function testGetEndpointsForUnknownVersionReturnsAll(): void
    {
        $future = Endpoints::forVersion(FMVersionMajor::FUTURE);

        self::assertSame(\count(Endpoints::all()), \count($future));
    }

    public function testCreateRecordHasAlsoMethodsPut(): void
    {
        $endpoint = Endpoints::get('createRecord');

        self::assertNotNull($endpoint);
        self::assertContains(HttpMethod::PUT, $endpoint->alsoMethods);
        self::assertSame('application/json', $endpoint->contentType);
    }

    public function testAddFieldsUsesSlashPath(): void
    {
        $endpoint = Endpoints::get('addFields');

        self::assertNotNull($endpoint);
        self::assertSame(HttpMethod::PATCH, $endpoint->method);
        self::assertSame('/{database}/FileMaker_Tables/{tableName}', $endpoint->path);
        self::assertStringNotContainsString("'", $endpoint->path);
    }

    public function testDeleteTableUsesSlashPath(): void
    {
        $endpoint = Endpoints::get('deleteTable');

        self::assertNotNull($endpoint);
        self::assertSame(HttpMethod::DELETE, $endpoint->method);
        self::assertSame('/{database}/FileMaker_Tables/{tableName}', $endpoint->path);
    }

    public function testDeleteFieldUsesSlashPath(): void
    {
        $endpoint = Endpoints::get('deleteField');

        self::assertNotNull($endpoint);
        self::assertSame(
            '/{database}/FileMaker_Tables/{tableName}/{fieldName}',
            $endpoint->path,
        );
    }

    public function testCreateIndexUsesTableInPath(): void
    {
        $endpoint = Endpoints::get('createIndex');

        self::assertNotNull($endpoint);
        self::assertSame(HttpMethod::POST, $endpoint->method);
        self::assertSame('/{database}/FileMaker_Indexes/{tableName}', $endpoint->path);
    }

    public function testDeleteIndexUsesTableAndFieldInPath(): void
    {
        $endpoint = Endpoints::get('deleteIndex');

        self::assertNotNull($endpoint);
        self::assertSame(HttpMethod::DELETE, $endpoint->method);
        self::assertSame(
            '/{database}/FileMaker_Indexes/{tableName}/{fieldName}',
            $endpoint->path,
        );
    }

    public function testCreateTableUnchanged(): void
    {
        $endpoint = Endpoints::get('createTable');

        self::assertNotNull($endpoint);
        self::assertSame(HttpMethod::POST, $endpoint->method);
        self::assertSame('/{database}/FileMaker_Tables', $endpoint->path);
    }

    public function testGetAllWebhooksIsGet(): void
    {
        $endpoint = Endpoints::get('getAllWebhooks');

        self::assertNotNull($endpoint);
        self::assertSame(HttpMethod::GET, $endpoint->method);
        self::assertSame('/{database}/Webhook.GetAll', $endpoint->path);
    }

    public function testGetWebhookIsGetWithIdInPath(): void
    {
        $endpoint = Endpoints::get('getWebhook');

        self::assertNotNull($endpoint);
        self::assertSame(HttpMethod::GET, $endpoint->method);
        self::assertSame('/{database}/Webhook.Get({webhookId})', $endpoint->path);
    }

    public function testDeleteWebhookUsesDeleteNotRemove(): void
    {
        $endpoint = Endpoints::get('deleteWebhook');

        self::assertNotNull($endpoint);
        self::assertSame(HttpMethod::POST, $endpoint->method);
        self::assertSame('/{database}/Webhook.Delete({webhookId})', $endpoint->path);
        self::assertStringNotContainsString('Remove', $endpoint->path);
    }

    public function testInvokeWebhookHasIdInPath(): void
    {
        $endpoint = Endpoints::get('invokeWebhook');

        self::assertNotNull($endpoint);
        self::assertSame(HttpMethod::POST, $endpoint->method);
        self::assertSame('/{database}/Webhook.Invoke({webhookId})', $endpoint->path);
    }

    public function testCreateWebhookUnchanged(): void
    {
        $endpoint = Endpoints::get('createWebhook');

        self::assertNotNull($endpoint);
        self::assertSame(HttpMethod::POST, $endpoint->method);
        self::assertSame('/{database}/Webhook.Add', $endpoint->path);
    }
}
