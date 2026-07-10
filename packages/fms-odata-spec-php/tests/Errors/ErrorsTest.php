<?php

declare(strict_types=1);

namespace FmsOData\Spec\Tests\Errors;

use FmsOData\Spec\Errors\FMAuthError;
use FmsOData\Spec\Errors\FMNotFoundError;
use FmsOData\Spec\Errors\FMODataError;
use FmsOData\Spec\Errors\FMScriptError;
use FmsOData\Spec\Errors\FMValidationError;
use FmsOData\Spec\Errors\Errors;
use FmsOData\Spec\Errors\ODataErrorBody;
use FmsOData\Spec\Errors\ODataErrorDetail;
use FmsOData\Spec\Errors\ODataErrorInner;
use FmsOData\Spec\Errors\RequestRef;
use PHPUnit\Framework\TestCase;

final class ErrorsTest extends TestCase
{
    public function test_fm_odata_error_carries_status_and_code(): void
    {
        $e = new FMODataError('boom', status: 500, odataCode: 'X100');
        self::assertSame(500, $e->status);
        self::assertSame('X100', $e->odataCode);
        self::assertSame('boom', $e->getMessage());
        self::assertNull($e->odataError);
        self::assertNull($e->request);
    }

    public function test_fm_odata_error_with_request_ref(): void
    {
        $req = new RequestRef(method: 'GET', url: 'https://x/y');
        $e = new FMODataError('boom', status: 500, request: $req);
        self::assertNotNull($e->request);
        self::assertSame('GET', $e->request->method);
        self::assertSame('https://x/y', $e->request->url);
    }

    public function test_fm_script_error_status_is_200(): void
    {
        $e = new FMScriptError('script failed', scriptError: 101, scriptResult: 'missing');
        self::assertSame(200, $e->status);
        self::assertSame('101', $e->odataCode);
        self::assertSame(101, $e->scriptError);
        self::assertSame('missing', $e->scriptResult);
        self::assertInstanceOf(FMODataError::class, $e);
    }

    public function test_fm_auth_error_status_401(): void
    {
        $e = new FMAuthError('nope');
        self::assertSame(401, $e->status);
        self::assertInstanceOf(FMODataError::class, $e);
    }

    public function test_fm_not_found_error_status_404(): void
    {
        $e = new FMNotFoundError('missing');
        self::assertSame(404, $e->status);
    }

    public function test_fm_validation_error_status_400(): void
    {
        $body = new ODataErrorBody(
            error: new ODataErrorInner(
                code: 'V1',
                message: 'bad',
                details: [new ODataErrorDetail(code: 'D', message: 'd')],
            ),
        );
        $e = new FMValidationError('bad', odataError: $body);
        self::assertSame(400, $e->status);
        self::assertNotNull($e->odataError);
        self::assertSame('D', $e->odataError->error->details[0]->code);
    }

    public function test_is_fm_odata_error_true_for_subclasses(): void
    {
        self::assertTrue(Errors::isFMODataError(new FMAuthError('x')));
        self::assertTrue(Errors::isFMODataError(new FMScriptError('x', scriptError: 1)));
        self::assertFalse(Errors::isFMODataError(new \ValueError('x')));
    }

    public function test_is_fm_script_error_distinguishes(): void
    {
        self::assertTrue(Errors::isFMScriptError(new FMScriptError('x', scriptError: 1)));
        self::assertFalse(Errors::isFMScriptError(new FMAuthError('x')));
        self::assertFalse(Errors::isFMScriptError(new \ValueError('x')));
    }

    public function test_errors_are_raisable_and_catchable(): void
    {
        $this->expectException(FMODataError::class);
        throw new FMAuthError('nope');
    }
}
