<?php

declare(strict_types=1);

namespace FmsOData\Spec\Auth;

/** Authentication scheme supported by FileMaker OData. */
enum FMAuthScheme: string
{
    case BASIC = 'Basic';
    case BEARER = 'Bearer';
}
