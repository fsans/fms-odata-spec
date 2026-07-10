<?php

declare(strict_types=1);

namespace FmsOData\Spec\Endpoints;

/** Endpoint category. */
enum EndpointCategory: string
{
    case DISCOVERY = 'discovery';
    case METADATA = 'metadata';
    case QUERY = 'query';
    case CRUD = 'crud';
    case BATCH = 'batch';
    case SCRIPTS = 'scripts';
    case CONTAINERS = 'containers';
    case SCHEMA = 'schema';
    case WEBHOOKS = 'webhooks';
}
