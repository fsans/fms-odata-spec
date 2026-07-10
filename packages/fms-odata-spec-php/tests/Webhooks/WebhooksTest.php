<?php

declare(strict_types=1);

namespace FmsOData\Spec\Tests\Webhooks;

use FmsOData\Spec\Webhooks\WebhookCreateParams;
use FmsOData\Spec\Webhooks\WebhookCreateResult;
use FmsOData\Spec\Webhooks\WebhookData;
use FmsOData\Spec\Webhooks\WebhookInvokeParams;
use FmsOData\Spec\Webhooks\WebhookOperation;
use FmsOData\Spec\Webhooks\Webhooks;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Webhooks domain, ported from test_webhooks.py.
 */
final class WebhooksTest extends TestCase
{
    public function testWebhookPathWithoutId(): void
    {
        self::assertSame('/MyDB/Webhook.Add', Webhooks::path('MyDB', WebhookOperation::ADD));
        self::assertSame('/MyDB/Webhook.GetAll', Webhooks::path('MyDB', WebhookOperation::GET_ALL));
    }

    public function testWebhookPathWithId(): void
    {
        self::assertSame('/MyDB/Webhook.Get(1)', Webhooks::path('MyDB', WebhookOperation::GET, 1));
        self::assertSame('/MyDB/Webhook.Delete(42)', Webhooks::path('MyDB', WebhookOperation::DELETE, 42));
        self::assertSame('/MyDB/Webhook.Invoke(7)', Webhooks::path('MyDB', WebhookOperation::INVOKE, 7));
    }

    public function testWebhookOperationHasDeleteNotRemove(): void
    {
        self::assertSame('/DB/Webhook.Delete(1)', Webhooks::path('DB', WebhookOperation::DELETE, 1));
    }

    public function testWebhookCreateParamsToODataArray(): void
    {
        $params = new WebhookCreateParams(
            'https://example.com/hook',
            'Customers',
            endpointHeaders: ['X-Token' => 'abc'],
            notifySchemaChanges: true,
            select: 'id,name',
            filter: 'status eq \'open\'',
            maxFailedAttempts: 3,
        );
        $array = $params->toODataArray();

        self::assertSame('https://example.com/hook', $array['webhook']);
        self::assertSame('Customers', $array['tableName']);
        self::assertSame(['X-Token' => 'abc'], $array['endpointHeaders']);
        self::assertTrue($array['notifySchemaChanges']);
        self::assertSame('id,name', $array['select']);
        self::assertSame('status eq \'open\'', $array['filter']);
        self::assertSame(3, $array['maxFailedAttempts']);
    }

    public function testWebhookCreateParamsMinimal(): void
    {
        $params = new WebhookCreateParams('u', 't');

        self::assertSame(['webhook' => 'u', 'tableName' => 't'], $params->toODataArray());
    }

    public function testWebhookDataToODataArray(): void
    {
        $data = new WebhookData(
            'https://example.com/hook',
            'Customers',
            webhookId: 5,
            queryHeaders: ['X' => 'y'],
        );
        $array = $data->toODataArray();

        self::assertSame(5, $array['webhookID']);
        self::assertSame(['X' => 'y'], $array['queryHeaders']);
        self::assertSame('Customers', $array['tableName']);
    }

    public function testWebhookDataLegacyIdStillSupported(): void
    {
        $data = new WebhookData(
            'https://example.com/hook',
            'Customers',
            id: 'abc',
        );
        $array = $data->toODataArray();

        self::assertSame('abc', $array['id']);
        self::assertArrayNotHasKey('webhookID', $array);
    }

    public function testWebhookInvokeParamsToODataArray(): void
    {
        $params = new WebhookInvokeParams([1, 2, 3]);

        self::assertSame(['rowIDs' => [1, 2, 3]], $params->toODataArray());
    }

    public function testWebhookInvokeParamsEmptyIsValid(): void
    {
        $params = new WebhookInvokeParams();

        self::assertSame(['rowIDs' => []], $params->toODataArray());
    }

    public function testWebhookCreateResultRoundtrip(): void
    {
        $result = new WebhookCreateResult(11);

        self::assertSame(['webhookResult' => ['webhookID' => 11]], $result->toODataArray());
    }

    public function testWebhookCreateResultFromODataArray(): void
    {
        $result = WebhookCreateResult::fromODataArray(['webhookResult' => ['webhookID' => 11]]);

        self::assertSame(11, $result->webhookId);
    }
}
