<?php

declare(strict_types=1);

namespace FmsOData\Spec\Versions;

/**
 * OData protocol version for a specific FileMaker Server version.
 *
 * v20.x implements OData 4.0. v21.x onward implements partial OData 4.01
 * at intermediate conformance level with some exceptions.
 */
enum ODataProtocolVersion: string
{
    case V4_0 = '4.0';
    case V4_01 = '4.01';
}
