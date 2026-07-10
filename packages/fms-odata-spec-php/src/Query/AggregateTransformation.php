<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** $apply aggregate transformation. */
final readonly class AggregateTransformation implements ApplyTransformation
{
    /** @param list<AggregateExpression> $expressions */
    public function __construct(
        public array $expressions,
    ) {}

    public function type(): string
    {
        return 'aggregate';
    }
}
