<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** Explicitly unsupported $filter functions. */
enum UnsupportedFilterFunction: string
{
    case FRACTIONAL_SECONDS = 'fractionalseconds';
    case IS_OF = 'isof';
    case GEO_DISTANCE = 'geo.distance';
    case GEO_LENGTH = 'geo.length';
    case GEO_INTERSECTS = 'geo.intersects';
    case ANY = 'any';
    case ALL = 'all';
}
