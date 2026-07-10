<?php

declare(strict_types=1);

namespace FmsOData\Spec\Endpoints;

/** HTTP methods used by the OData API. */
enum HttpMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PATCH = 'PATCH';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
}
