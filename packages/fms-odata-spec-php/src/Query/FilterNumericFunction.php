<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** Supported $filter numeric functions. */
enum FilterNumericFunction: string
{
    case ROUND = 'round';
    case FLOOR = 'floor';
    case CEILING = 'ceiling';
}
