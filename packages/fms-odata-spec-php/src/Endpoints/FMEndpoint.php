<?php

declare(strict_types=1);

namespace FmsOData\Spec\Endpoints;

use FmsOData\Spec\Versions\FMVersionMajor;

/**
 * Endpoint descriptor.
 *
 * Immutable readonly DTO mirroring FMEndpoint from the TS/Python packages.
 */
final readonly class FMEndpoint
{
    /**
     * @param list<HttpMethod> $alsoMethods Additional HTTP methods that also work.
     * @param ?string $contentType Content-Type required for the request body.
     * @param list<string> $queryOptions Query options supported by this endpoint.
     */
    public function __construct(
        public string $id,
        public HttpMethod $method,
        public string $path,
        public string $description,
        public FMVersionMajor $minVersion,
        public EndpointCategory $category,
        public array $alsoMethods = [],
        public ?string $contentType = null,
        public array $queryOptions = [],
    ) {}
}
