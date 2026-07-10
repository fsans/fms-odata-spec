<?php

declare(strict_types=1);

namespace FmsOData\Spec\Batch;

/** Result of a single batch operation. */
final readonly class BatchOpResult
{
    /**
     * @template T
     * @param int $status HTTP status code.
     * @param array<string, string> $headers Response headers.
     * @param bool $ok Whether the status is 2xx.
     * @param T|null $body Response body.
     * @param ?int $contentId Content-ID if specified in the request.
     */
    public function __construct(
        public int $status,
        public array $headers,
        public bool $ok,
        public mixed $body = null,
        public ?int $contentId = null,
    ) {}
}
