<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** $apply groupby transformation. */
final readonly class GroupByTransformation implements ApplyTransformation
{
    public function __construct(
        public GroupByExpression $expression,
    ) {}

    public function type(): string
    {
        return 'groupby';
    }
}
