<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** Sort direction for $orderby. */
enum SortDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';
}
