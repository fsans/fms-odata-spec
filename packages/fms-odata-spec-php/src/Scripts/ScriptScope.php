<?php

declare(strict_types=1);

namespace FmsOData\Spec\Scripts;

/** Script invocation scope. */
enum ScriptScope: string
{
    case DATABASE = 'database';
    case ENTITY_SET = 'entitySet';
    case RECORD = 'record';
}
