<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** $apply groupby expression. */
final readonly class GroupByExpression
{
    /**
     * @param list<string> $fields
     * @param list<AggregateExpression>|null $aggregate
     */
    public function __construct(
        public array $fields,
        public ?array $aggregate = null,
    ) {}
}
