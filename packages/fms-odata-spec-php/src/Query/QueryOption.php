<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** Supported system query options. */
enum QueryOption: string
{
    case FILTER = '$filter';
    case SELECT = '$select';
    case ORDERBY = '$orderby';
    case TOP = '$top';
    case SKIP = '$skip';
    case EXPAND = '$expand';
    case COUNT = '$count';
    case APPLY = '$apply';
    case SEARCH = '$search';
    case COMPUTE = '$compute';
}
