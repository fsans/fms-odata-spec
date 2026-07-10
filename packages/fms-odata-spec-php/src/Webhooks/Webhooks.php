<?php

declare(strict_types=1);

namespace FmsOData\Spec\Webhooks;

/**
 * Static facade for webhook helpers.
 *
 * Mirrors webhookPath() from the TS/Python packages.
 */
final class Webhooks
{
    /**
     * Build the URL path for a webhook operation.
     *
     * When $id is provided, it is appended as an OData function argument, e.g.
     * Webhook.Get(1). When omitted, the bare operation path is returned, e.g.
     * Webhook.GetAll.
     */
    public static function path(string $database, WebhookOperation $operation, ?int $id = null): string
    {
        if ($id === null) {
            return '/' . $database . '/Webhook.' . $operation->value;
        }

        return '/' . $database . '/Webhook.' . $operation->value . '(' . $id . ')';
    }
}
