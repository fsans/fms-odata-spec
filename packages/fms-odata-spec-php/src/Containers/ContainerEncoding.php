<?php

declare(strict_types=1);

namespace FmsOData\Spec\Containers;

/** Upload encoding mode. */
enum ContainerEncoding: string
{
    case BINARY = 'binary';
    case BASE64 = 'base64';
}
