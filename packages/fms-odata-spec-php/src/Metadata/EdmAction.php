<?php

declare(strict_types=1);

namespace FmsOData\Spec\Metadata;

/** Edm action (FileMaker script). */
final readonly class EdmAction
{
    public function __construct(
        public string $name,
        public bool $isBound,
        public ?string $scriptId = null,
        public ?string $parameterType = null,
        public ?string $returnType = null,
    ) {}
}
