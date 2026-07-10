<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** Supported $filter logical operators. */
enum FilterLogicalOp: string
{
    case AND = 'and';
    case OR = 'or';
    case NOT = 'not';
}
