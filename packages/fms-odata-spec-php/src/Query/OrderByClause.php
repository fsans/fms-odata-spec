<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** $orderby clause. */
final readonly class OrderByClause
{
    public function __construct(
        public string $field,
        public ?SortDirection $direction = null,
    ) {}
}
