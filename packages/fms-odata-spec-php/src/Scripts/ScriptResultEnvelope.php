<?php

declare(strict_types=1);

namespace FmsOData\Spec\Scripts;

/** Script result envelope from the OData API. */
final readonly class ScriptResultEnvelope
{
    public function __construct(
        public ScriptResultInner $scriptResult,
    ) {}
}
