<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/** Query parameters for a record query. */
final readonly class QueryParams
{
    /**
     * @param ?string $filter
     * @param list<string>|null $select
     * @param list<OrderByClause>|null $orderby
     * @param ?int $top
     * @param ?int $skip
     * @param string|list<string>|null $expand
     * @param ?bool $count
     * @param ?string $apply
     */
    public function __construct(
        public ?string $filter = null,
        public ?array $select = null,
        public ?array $orderby = null,
        public ?int $top = null,
        public ?int $skip = null,
        public string|array|null $expand = null,
        public ?bool $count = null,
        public ?string $apply = null,
    ) {}
}
