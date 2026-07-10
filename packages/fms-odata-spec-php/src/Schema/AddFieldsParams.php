<?php

declare(strict_types=1);

namespace FmsOData\Spec\Schema;

/** Parameters for adding fields to an existing table. */
final readonly class AddFieldsParams
{
    /** @param list<FMFieldDefinition> $fields */
    public function __construct(
        public string $tableName,
        public array $fields = [],
    ) {}

    /** @return array<string, mixed> */
    public function toODataArray(): array
    {
        return [
            'tableName' => $this->tableName,
            'fields' => \array_map(fn (FMFieldDefinition $f): array => $f->toODataArray(), $this->fields),
        ];
    }
}
