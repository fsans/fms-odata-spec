<?php

declare(strict_types=1);

namespace FmsOData\Spec\Scripts;

/** Script identifier by name. */
final readonly class ScriptNameId implements ScriptIdentifier
{
    public function __construct(
        public string $name,
    ) {}

    public function type(): string
    {
        return 'name';
    }
}
