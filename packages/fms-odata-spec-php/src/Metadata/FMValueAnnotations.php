<?php

declare(strict_types=1);

namespace FmsOData\Spec\Metadata;

/** FileMaker-specific value annotations. */
final readonly class FMValueAnnotations
{
    public function __construct(
        public ?int $maxRepetitions = null,
        public ?string $externalSecurePath = null,
        public ?string $bestRowId = null,
        public ?string $rowVersion = null,
        public ?string $tableId = null,
        public ?string $fieldId = null,
        public ?string $scriptId = null,
        public ?string $fmComment = null,
        public ?string $aiAnnotation = null,
    ) {}
}
