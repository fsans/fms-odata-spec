<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** Query result (simplified). */
final readonly class QueryResult
{
    /**
     * @template T
     * @param list<T> $value
     * @param ?int $count
     * @param ?string $nextLink
     */
    public function __construct(
        public array $value,
        public ?int $count = null,
        public ?string $nextLink = null,
    ) {}
}
