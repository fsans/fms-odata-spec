<?php

declare(strict_types=1);

namespace FmsOData\Spec\Webhooks;

/** Result returned by Webhook.Add. */
final readonly class WebhookCreateResult
{
    public function __construct(
        public int $webhookId,
    ) {}

    /**
     * Build from the raw OData wire array {"webhookResult": {"webhookID": n}}.
     *
     * @param array<string, mixed> $data
     */
    public static function fromODataArray(array $data): self
    {
        $result = $data['webhookResult'] ?? [];
        $rawId = \is_array($result) ? ($result['webhookID'] ?? 0) : 0;
        $webhookId = \is_numeric($rawId) ? (int) $rawId : 0;

        return new self(webhookId: $webhookId);
    }

    /** @return array<string, array<string, int>> */
    public function toODataArray(): array
    {
        return ['webhookResult' => ['webhookID' => $this->webhookId]];
    }
}
