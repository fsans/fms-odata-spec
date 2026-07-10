<?php

declare(strict_types=1);

namespace FmsOData\Spec\Versions;

/**
 * Query option availability for a specific version.
 *
 * Immutable readonly DTO mirroring FMQueryOptionFlags from the TS/Python
 * packages. Property names use camelCase without the leading `$`.
 */
final readonly class FMQueryOptionFlags
{
    public function __construct(
        public bool $filter,
        public bool $select,
        public bool $orderby,
        public bool $top,
        public bool $skip,
        public bool $expand,
        public bool $count,
        public bool $apply,
        public bool $search,
        public bool $compute,
    ) {}

    /**
     * Check whether a query option is enabled by its camelCase property name.
     *
     * @param string $option e.g. "filter", "apply"
     */
    public function has(string $option): bool
    {
        return \property_exists($this, $option) && $this->{$option} === true;
    }
}
