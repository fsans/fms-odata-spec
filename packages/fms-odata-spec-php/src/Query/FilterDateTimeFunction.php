<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** Supported $filter date/time functions. */
enum FilterDateTimeFunction: string
{
    case YEAR = 'year';
    case MONTH = 'month';
    case DAY = 'day';
    case HOUR = 'hour';
    case MINUTE = 'minute';
    case SECOND = 'second';
    case DATE = 'date';
    case TIME = 'time';
}
