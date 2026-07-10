<?php

declare(strict_types=1);

namespace FmsOData\Spec\Auth;

/**
 * Configuration for OAuth/Bearer auth (external identity providers, FileMaker Cloud).
 *
 * Immutable readonly DTO. The scheme is validated in the constructor.
 *
 * The optional `$onUnauthorized` callback is typed as a Closure but this
 * package does not invoke it or impose a promise type. Downstream code is
 * responsible for wiring up token refresh on 401 responses.
 */
final readonly class FMOAuthAuthConfig
{
    public FMAuthScheme $scheme;

    /**
     * @param ?\Closure(): string $onUnauthorized Optional refresh callback invoked on 401.
     */
    public function __construct(
        public string $token,
        public ?\Closure $onUnauthorized = null,
        ?FMAuthScheme $scheme = null,
    ) {
        $this->scheme = $scheme ?? FMAuthScheme::BEARER;
        if ($this->scheme !== FMAuthScheme::BEARER) {
            throw new \InvalidArgumentException(
                \sprintf("FMOAuthAuthConfig.scheme must be 'Bearer', got '%s'", $this->scheme->value),
            );
        }
    }
}
