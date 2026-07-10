<?php

declare(strict_types=1);

namespace FmsOData\Spec\Webhooks;

/**
 * Webhook data returned by Webhook.Get / Webhook.GetAll.
 *
 * FMS uses webhookID (an integer) as the primary key in responses. The
 * optional id field is kept for backward compatibility; callers should
 * prefer webhookID and map it to id if needed.
 */
final readonly class WebhookData
{
    /**
     * @param ?int $webhookId Integer id assigned by FMS (primary key in responses).
     * @param ?string $id Legacy/optional id field. Prefer webhookId.
     * @param ?array<string, string> $endpointHeaders
     * @param ?array<string, string> $queryHeaders
     */
    public function __construct(
        public string $webhook,
        public string $tableName,
        public ?int $webhookId = null,
        public ?string $id = null,
        public ?array $endpointHeaders = null,
        public ?array $queryHeaders = null,
        public ?bool $notifySchemaChanges = null,
        public ?string $select = null,
        public ?string $filter = null,
        public ?int $maxFailedAttempts = null,
    ) {}

    /** @return array<string, mixed> */
    public function toODataArray(): array
    {
        $out = [
            'webhook' => $this->webhook,
            'tableName' => $this->tableName,
        ];
        if ($this->webhookId !== null) {
            $out['webhookID'] = $this->webhookId;
        }
        if ($this->id !== null) {
            $out['id'] = $this->id;
        }
        if ($this->endpointHeaders !== null) {
            $out['endpointHeaders'] = $this->endpointHeaders;
        }
        if ($this->queryHeaders !== null) {
            $out['queryHeaders'] = $this->queryHeaders;
        }
        if ($this->notifySchemaChanges !== null) {
            $out['notifySchemaChanges'] = $this->notifySchemaChanges;
        }
        if ($this->select !== null) {
            $out['select'] = $this->select;
        }
        if ($this->filter !== null) {
            $out['filter'] = $this->filter;
        }
        if ($this->maxFailedAttempts !== null) {
            $out['maxFailedAttempts'] = $this->maxFailedAttempts;
        }

        return $out;
    }
}
