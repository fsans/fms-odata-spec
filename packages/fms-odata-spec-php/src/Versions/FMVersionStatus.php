<?php

declare(strict_types=1);

namespace FmsOData\Spec\Versions;

/** Version status. */
enum FMVersionStatus: string
{
    case SUPPORTED = 'supported';
    case CURRENT = 'current';
    case FUTURE = 'future';
}
