<?php

declare(strict_types=1);

namespace FmsOData\Spec\Webhooks;

/**
 * Webhook operation types.
 *
 * Note: FMS exposes Webhook.Delete, not Webhook.Remove.
 */
enum WebhookOperation: string
{
    case ADD = 'Add';
    case DELETE = 'Delete';
    case GET = 'Get';
    case GET_ALL = 'GetAll';
    case INVOKE = 'Invoke';
}
