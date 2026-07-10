<?php

declare(strict_types=1);

namespace FmsOData\Spec\Scripts;

/** Script descriptor from metadata. */
final readonly class ScriptDescriptor
{
    public function __construct(
        public string $name,
        public bool $isBound,
        public ?string $fmsid = null,
        public ?string $parameterType = null,
        public ?string $returnType = null,
    ) {}
}
