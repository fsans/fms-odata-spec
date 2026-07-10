<?php

declare(strict_types=1);

namespace FmsOData\Spec\Batch;

/** Batch operation type. */
enum BatchOpType: string
{
    case LIST = 'list';
    case GET = 'get';
    case CREATE = 'create';
    case PATCH = 'patch';
    case PUT = 'put';
    case DELETE = 'delete';
}
