<?php

declare(strict_types=1);

namespace FmsOData\Spec\Auth;

use FmsOData\Spec\Versions\ODataProtocolVersion;

/**
 * Standard auth-related headers.
 *
 * Immutable readonly DTO. Use {@see FMAuthHeaders::toArray()} to emit the
 * wire-format header keys (dropping unset entries).
 */
final readonly class FMAuthHeaders
{
    public function __construct(
        public string $authorization,
        public ?ODataProtocolVersion $odataVersion = null,
        public ?ODataProtocolVersion $odataMaxVersion = null,
    ) {}

    /**
     * Return the headers as a plain array, dropping unset entries.
     *
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $result = ['Authorization' => $this->authorization];
        if ($this->odataVersion !== null) {
            $result['OData-Version'] = $this->odataVersion->value;
        }
        if ($this->odataMaxVersion !== null) {
            $result['OData-MaxVersion'] = $this->odataMaxVersion->value;
        }

        return $result;
    }
}
