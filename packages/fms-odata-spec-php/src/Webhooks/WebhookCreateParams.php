<?php

declare(strict_types=1);

namespace FmsOData\Spec\Webhooks;

/**
 * Parameters for creating a webhook.
 *
 * Property names are camelCase in PHP; use {@see WebhookCreateParams::toODataArray()}
 * to emit the original camelCase wire keys for JSON serialization.
 */
final readonly class WebhookCreateParams
{
    /**
     * @param ?array<string, string> $endpointHeaders Headers sent to the endpoint URL.
     * @param ?array<string, string> $headers Legacy alias for endpointHeaders.
     * @param ?array<string, string> $queryHeaders Headers controlling webhook payload generation.
     */
    public function __construct(
        public string $webhook,
        public string $tableName,
        public ?array $endpointHeaders = null,
        public ?array $headers = null,
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
        if ($this->endpointHeaders !== null) {
            $out['endpointHeaders'] = $this->endpointHeaders;
        }
        if ($this->headers !== null) {
            $out['headers'] = $this->headers;
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
