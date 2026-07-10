<?php

declare(strict_types=1);

namespace FmsOData\Spec\Tests\Scripts;

use FmsOData\Spec\Scripts\ScriptDescriptor;
use FmsOData\Spec\Scripts\ScriptFmsidId;
use FmsOData\Spec\Scripts\ScriptNameId;
use FmsOData\Spec\Scripts\ScriptOptions;
use FmsOData\Spec\Scripts\ScriptResult;
use FmsOData\Spec\Scripts\Scripts;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Scripts domain, ported from test_scripts.py.
 */
final class ScriptsTest extends TestCase
{
    public function testScriptErrorCodes(): void
    {
        self::assertSame(0, Scripts::ERROR_CODES['SUCCESS']);
        self::assertSame(101, Scripts::ERROR_CODES['RECORD_MISSING']);
        self::assertSame(401, Scripts::ERROR_CODES['NO_RECORDS_FOUND']);
    }

    public function testScriptPathSegmentByName(): void
    {
        self::assertSame('Script.MyScript', Scripts::pathSegment(new ScriptNameId('MyScript')));
    }

    public function testScriptPathSegmentByFmsid(): void
    {
        self::assertSame('Script.FMSID:42', Scripts::pathSegment(new ScriptFmsidId(42)));
    }

    public function testScriptRequestBodyNoneWhenNoOptions(): void
    {
        self::assertNull(Scripts::requestBody(null));
    }

    public function testScriptRequestBodyNoneWhenNoParameter(): void
    {
        self::assertNull(Scripts::requestBody(new ScriptOptions()));
    }

    public function testScriptRequestBodyWithStringParameter(): void
    {
        $body = Scripts::requestBody(new ScriptOptions('hello'));

        self::assertNotNull($body);
        self::assertSame(['scriptParameterValue' => 'hello'], \json_decode($body, true));
    }

    public function testScriptRequestBodyWithArrayParameter(): void
    {
        $body = Scripts::requestBody(new ScriptOptions(['k' => 1]));

        self::assertNotNull($body);
        self::assertSame(['scriptParameterValue' => ['k' => 1]], \json_decode($body, true));
    }

    public function testScriptRequestBodyWithIntParameter(): void
    {
        $body = Scripts::requestBody(new ScriptOptions(7));

        self::assertNotNull($body);
        self::assertSame(['scriptParameterValue' => 7], \json_decode($body, true));
    }

    public function testParseScriptResponseNestedEnvelope(): void
    {
        $raw = ['scriptResult' => ['code' => 0, 'resultParameter' => 'Hello World']];
        $result = Scripts::parseResponse($raw);

        self::assertSame(0, $result->code);
        self::assertSame('Hello World', $result->resultParameter);
        self::assertSame($raw, $result->raw);
    }

    public function testParseScriptResponseNonzeroCode(): void
    {
        $raw = ['scriptResult' => ['code' => 101, 'resultParameter' => 'missing']];
        $result = Scripts::parseResponse($raw);

        self::assertSame(101, $result->code);
        self::assertSame('missing', $result->resultParameter);
    }

    public function testParseScriptResponseMissingCodeDefaultsZero(): void
    {
        $raw = ['scriptResult' => ['resultParameter' => 'x']];
        $result = Scripts::parseResponse($raw);

        self::assertSame(0, $result->code);
        self::assertSame('x', $result->resultParameter);
    }

    public function testParseScriptResponseFlatFallback(): void
    {
        $raw = ['unrelated' => 1];
        $result = Scripts::parseResponse($raw);

        self::assertSame(0, $result->code);
        self::assertSame($raw, $result->raw);
    }

    public function testParseScriptResponseNonDict(): void
    {
        self::assertSame(0, Scripts::parseResponse(null)->code);
        self::assertSame(0, Scripts::parseResponse('string')->code);
        self::assertSame(0, Scripts::parseResponse(42)->code);
    }

    public function testScriptDescriptorConstructs(): void
    {
        $descriptor = new ScriptDescriptor('MyScript', true, '123');

        self::assertTrue($descriptor->isBound);
        self::assertSame('123', $descriptor->fmsid);
    }

    public function testScriptOptionsDefaults(): void
    {
        $options = new ScriptOptions();

        self::assertNull($options->parameter);
        self::assertNull($options->cancelToken);
    }

    public function testParseScriptResponseNonNumericCodeDefaultsZero(): void
    {
        $raw = ['scriptResult' => ['code' => 'N/A', 'resultParameter' => 'x']];
        $result = Scripts::parseResponse($raw);

        self::assertSame(0, $result->code);
        self::assertSame('x', $result->resultParameter);
    }
}
