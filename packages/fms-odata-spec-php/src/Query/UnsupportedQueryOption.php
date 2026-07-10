<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** Query options not supported by FileMaker. */
enum UnsupportedQueryOption: string
{
    case SEARCH = '$search';
    case COMPUTE = '$compute';
}
