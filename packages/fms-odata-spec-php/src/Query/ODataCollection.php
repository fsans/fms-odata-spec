<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/**
 * Standard OData response envelope for a collection.
 *
 * @template T
 */
final readonly class ODataCollection
{
    /**
     * @param string $odataContext The @odata.context value.
     * @param list<T> $value The collection items.
     * @param ?int $odataCount The @odata.count value.
     * @param ?string $odataNextLink The @odata.nextLink value.
     */
    public function __construct(
        public string $odataContext,
        public array $value,
        public ?int $odataCount = null,
        public ?string $odataNextLink = null,
    ) {}
}
