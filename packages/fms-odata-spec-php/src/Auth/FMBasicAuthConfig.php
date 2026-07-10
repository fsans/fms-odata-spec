<?php

declare(strict_types=1);

namespace FmsOData\Spec\Auth;

/**
 * Configuration for Basic auth (FileMaker Server on-premise).
 *
 * Immutable readonly DTO. The scheme is validated in the constructor.
 */
final readonly class FMBasicAuthConfig
{
    public FMAuthScheme $scheme;

    public function __construct(
        public string $account,
        public string $password,
        ?FMAuthScheme $scheme = null,
    ) {
        $this->scheme = $scheme ?? FMAuthScheme::BASIC;
        if ($this->scheme !== FMAuthScheme::BASIC) {
            throw new \InvalidArgumentException(
                \sprintf("FMBasicAuthConfig.scheme must be 'Basic', got '%s'", $this->scheme->value),
            );
        }
    }
}
