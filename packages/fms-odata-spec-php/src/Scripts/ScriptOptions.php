<?php

declare(strict_types=1);

namespace FmsOData\Spec\Scripts;

/**
 * Options for running a script.
 *
 * The TS `signal?: AbortSignal` field has no direct PHP equivalent; it is
 * replaced with an optional opaque cancellation object. This package does
 * not act on it — downstream code is responsible for wiring cancellation.
 */
final readonly class ScriptOptions
{
    /**
     * @param string|int|float|array<string, mixed>|null $parameter
     * @param object|null $cancelToken Opaque cancellation placeholder (not acted on by this package).
     */
    public function __construct(
        public string|int|float|array|null $parameter = null,
        public ?object $cancelToken = null,
    ) {}
}
