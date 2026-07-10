<?php

declare(strict_types=1);

namespace FmsOData\Spec\Webhooks;

/**
 * Body for Webhook.Invoke({id}).
 *
 * rowIDs is required (an empty array is valid and triggers the webhook for
 * all pending records). An absent body is rejected by FMS with a JSON
 * syntax error.
 */
final readonly class WebhookInvokeParams
{
    /** @param list<string|int> $rowIds */
    public function __construct(
        public array $rowIds = [],
    ) {}

    /** @return array<string, list<string|int>> */
    public function toODataArray(): array
    {
        return ['rowIDs' => $this->rowIds];
    }
}
