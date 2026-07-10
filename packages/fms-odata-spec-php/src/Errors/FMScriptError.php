<?php

declare(strict_types=1);

namespace FmsOData\Spec\Errors;

/**
 * Error thrown when a FileMaker script returns a non-zero exit code.
 *
 * Script errors return HTTP 200 with the error in the body, so the default
 * status is 200.
 */
class FMScriptError extends FMODataError
{
    /** Script error code (from scriptResult.code). */
    public readonly int $scriptError;

    /** Script result parameter (from scriptResult.resultParameter). */
    public readonly ?string $scriptResult;

    /**
     * @param string $message Error message.
     * @param int $scriptError Script error code.
     * @param ?string $scriptResult Script result parameter.
     * @param ?RequestRef $request Request reference.
     */
    public function __construct(
        string $message,
        int $scriptError,
        ?string $scriptResult = null,
        ?RequestRef $request = null,
    ) {
        parent::__construct(
            $message,
            status: 200,
            odataCode: (string) $scriptError,
            request: $request,
        );
        $this->scriptError = $scriptError;
        $this->scriptResult = $scriptResult;
    }
}
