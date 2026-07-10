<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** $apply aggregate expression. */
final readonly class AggregateExpression
{
    public function __construct(
        public string $field,
        public AggregateFunction $function,
        public string $alias,
        /** Optional offset added to the field value before aggregation. */
        public ?int $add = null,
    ) {}
}
