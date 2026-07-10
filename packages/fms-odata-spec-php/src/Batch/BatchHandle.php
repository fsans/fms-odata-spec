<?php

declare(strict_types=1);

namespace FmsOData\Spec\Batch;

/**
 * Handle for tracking a queued operation's result.
 *
 * The TS `promise: Promise<BatchOpResult<T>>` has no standard PHP equivalent.
 * This is a types-only package, so the handle is kept transport-neutral using
 * an opaque mixed handle. Downstream code is responsible for resolving it.
 *
 * @template T
 */
final readonly class BatchHandle
{
    /**
     * @param mixed $handle Opaque transport-neutral handle (e.g. a Future, Promise, or callback).
     */
    public function __construct(
        public mixed $handle,
    ) {}
}
