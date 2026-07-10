<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** Supported $filter comparison operators. */
enum FilterComparisonOp: string
{
    case EQ = 'eq';
    case NE = 'ne';
    case GT = 'gt';
    case GE = 'ge';
    case LT = 'lt';
    case LE = 'le';
}
