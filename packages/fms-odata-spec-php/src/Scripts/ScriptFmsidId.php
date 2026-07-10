<?php

declare(strict_types=1);

namespace FmsOData\Spec\Scripts;

/** Script identifier by FMSID. */
final readonly class ScriptFmsidId implements ScriptIdentifier
{
    public function __construct(
        public int $id,
    ) {}

    public function type(): string
    {
        return 'fmsid';
    }
}
