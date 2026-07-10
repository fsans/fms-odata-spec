<?php

declare(strict_types=1);

namespace FmsOData\Spec\Query;

/**
 * Standard OData response envelope for a single entity.
 *
 * The TS type is `{ '@odata.context': string; '@odata.etag'?: string } & T`
 * (an intersection). PHP has no intersection type for arbitrary shapes, so
 * the entity payload is carried in the $entity property of type T. Callers
 * access entity fields via `$envelope->entity->field`.
 *
 * @template T
 */
final readonly class ODataEntity
{
    /**
     * @param string $odataContext The @odata.context value.
     * @param T $entity The entity payload.
     * @param ?string $odataEtag The @odata.etag value.
     */
    public function __construct(
        public string $odataContext,
        public mixed $entity,
        public ?string $odataEtag = null,
    ) {}
}
