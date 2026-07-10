<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/**
 * $apply transformation (either aggregate-only or groupby with optional aggregate).
 *
 * Discriminated by the type() method. Use AggregateTransformation or
 * GroupByTransformation for construction.
 */
interface ApplyTransformation
{
    /** Returns "aggregate" or "groupby". */
    public function type(): string;
}
