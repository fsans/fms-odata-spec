<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** $apply aggregate function. */
enum AggregateFunction: string
{
    case SUM = 'sum';
    case MIN = 'min';
    case MAX = 'max';
    case AVERAGE = 'average';
    case COUNT_DISTINCT = 'countdistinct';
}
