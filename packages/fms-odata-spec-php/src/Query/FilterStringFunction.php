<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** Supported $filter string functions. */
enum FilterStringFunction: string
{
    case STARTS_WITH = 'startswith';
    case ENDS_WITH = 'endswith';
    case CONTAINS = 'contains';
    case LENGTH = 'length';
    case TO_LOWER = 'tolower';
    case TO_UPPER = 'toupper';
    case TRIM = 'trim';
    case SUBSTRING = 'substring';
    case INDEX_OF = 'indexof';
    case CONCAT = 'concat';
}
