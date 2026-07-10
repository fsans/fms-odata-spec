<?php

declare(strict_types=1);

namespace FmsOData\Spec\Metadata;

/** FileMaker immutable ID types. */
enum ImmutableIdType: string
{
    case FMTID = 'FMTID';
    case FMFID = 'FMFID';
    case FMSID = 'FMSID';
}
