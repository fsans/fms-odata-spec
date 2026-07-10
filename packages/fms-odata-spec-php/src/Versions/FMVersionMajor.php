<?php

declare(strict_types=1);

namespace FmsOData\Spec\Versions;

/**
 * FileMaker Server version major numbers.
 *
 * Enum values are the wire/string identifiers used throughout the spec.
 */
enum FMVersionMajor: string
{
    case V20 = '20';
    case V21 = '21';
    case V22 = '22';
    case V26 = '26';
    case FUTURE = 'future';
}
