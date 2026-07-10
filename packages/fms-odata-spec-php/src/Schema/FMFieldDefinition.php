<?php

declare(strict_types=1);

namespace FmsOData\Spec\Schema;

/**
 * Field definition for table creation or adding fields.
 *
 * The $global property maps to the wire key "global" (a reserved word in PHP,
 * hence the trailing underscore is not needed — PHP allows `global` as a
 * property name, only as a keyword in certain contexts).
 */
final readonly class FMFieldDefinition
{
    /**
     * @param string $default Default value expression (any string accepted).
     * @param ?string $externalSecurePath Relative path for secure external storage (BLOB fields only).
     */
    public function __construct(
        public string $name,
        public string $type,
        public bool $primary = false,
        public bool $unique = false,
        public bool $nullable = true,
        public bool $global = false,
        public ?string $default = null,
        public ?string $externalSecurePath = null,
    ) {}

    /** @return array<string, mixed> */
    public function toODataArray(): array
    {
        $out = [
            'name' => $this->name,
            'type' => $this->type,
            'primary' => $this->primary,
            'unique' => $this->unique,
            'nullable' => $this->nullable,
            'global' => $this->global,
        ];
        if ($this->default !== null) {
            $out['default'] = $this->default;
        }
        if ($this->externalSecurePath !== null) {
            $out['externalSecurePath'] = $this->externalSecurePath;
        }

        return $out;
    }
}
