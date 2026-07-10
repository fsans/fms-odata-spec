<?php

declare(strict_types=1);

namespace FmsOData\Spec\Tests\Auth;

use FmsOData\Spec\Auth\Auth;
use FmsOData\Spec\Auth\FMAuthHeaders;
use FmsOData\Spec\Auth\FMAuthScheme;
use FmsOData\Spec\Auth\FMBasicAuthConfig;
use FmsOData\Spec\Auth\FMOAuthAuthConfig;
use FmsOData\Spec\Versions\ODataProtocolVersion;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for the Auth domain.
 *
 * Ports the behavior of the Python package's test_auth.py to PHPUnit,
 * exercising the auth helpers, config DTOs, and auth-header serialization.
 */
final class AuthTest extends TestCase
{
    public function testBasicAuthEncodesAccountPassword(): void
    {
        $expected = 'Basic ' . \base64_encode('admin:secret');

        self::assertSame($expected, Auth::basicAuth('admin', 'secret'));
    }

    public function testBasicAuthUnicode(): void
    {
        $raw = \mb_convert_encoding('user:päss', 'UTF-8', 'UTF-8');
        $expected = 'Basic ' . \base64_encode($raw);

        self::assertSame($expected, Auth::basicAuth('user', 'päss'));
    }

    public function testBearerAuthPrefixesToken(): void
    {
        self::assertSame('Bearer abc123', Auth::bearerAuth('abc123'));
    }

    #[DataProvider('normalizeAuthTokenProvider')]
    public function testNormalizeAuthToken(string $token, string $expected): void
    {
        self::assertSame($expected, Auth::normalizeAuthToken($token));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function normalizeAuthTokenProvider(): array
    {
        return [
            'basic prefix preserved' => ['Basic abc', 'Basic abc'],
            'bearer prefix preserved' => ['Bearer t0k', 'Bearer t0k'],
            'bare token becomes bearer' => ['baretoken', 'Bearer baretoken'],
        ];
    }

    public function testBasicAuthConfigConstructs(): void
    {
        $cfg = new FMBasicAuthConfig('a', 'b');

        self::assertSame('Basic', $cfg->scheme->value);
        self::assertSame('a', $cfg->account);
        self::assertSame('b', $cfg->password);
    }

    public function testBasicAuthConfigRejectsWrongScheme(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new FMBasicAuthConfig('a', 'b', FMAuthScheme::BEARER);
    }

    public function testOAuthAuthConfigConstructs(): void
    {
        $cfg = new FMOAuthAuthConfig('t');

        self::assertSame('Bearer', $cfg->scheme->value);
        self::assertSame('t', $cfg->token);
        self::assertNull($cfg->onUnauthorized);
    }

    public function testOAuthAuthConfigWithRefresh(): void
    {
        $refresh = static fn (): string => 'new';

        $cfg = new FMOAuthAuthConfig('t', $refresh);

        self::assertNotNull($cfg->onUnauthorized);
    }

    public function testAuthHeadersToDictDropsUnset(): void
    {
        $headers = new FMAuthHeaders('Basic x');

        self::assertSame(
            ['Authorization' => 'Basic x'],
            $headers->toArray(),
        );
    }

    public function testAuthHeadersToDictIncludesODataHeaders(): void
    {
        $headers = new FMAuthHeaders(
            'Basic x',
            ODataProtocolVersion::V4_01,
            ODataProtocolVersion::V4_01,
        );

        self::assertSame(
            [
                'Authorization' => 'Basic x',
                'OData-Version' => '4.01',
                'OData-MaxVersion' => '4.01',
            ],
            $headers->toArray(),
        );
    }
}
