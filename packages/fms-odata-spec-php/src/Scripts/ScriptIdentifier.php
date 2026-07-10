<?php

declare(strict_types=1);

namespace FmsOData\Spec\Scripts;

/** Common interface for script identifiers. */
interface ScriptIdentifier
{
    /** Returns "name" or "fmsid". */
    public function type(): string;
}
