<?php

declare(strict_types=1);

namespace FmsOData\Spec\Tests\Versions;

use FmsOData\Spec\Versions\FMVersionInfo;
use FmsOData\Spec\Versions\Versions;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for the Versions domain.
 *
 * Ports the behavior of the Python package's test_versions.py to PHPUnit,
 * exercising the version matrix, feature flags, query-option flags, and
 * protocol-version constants.
 */
final class VersionsTest extends TestCase
{
    public function testProtocolVersionIs401(): void
    {
        self::assertSame('4.01', Versions::ODATA_PROTOCOL_VERSION);
    }

    public function testConformanceLevelIsIntermediate(): void
    {
        self::assertSame('intermediate', Versions::ODATA_CONFORMANCE_LEVEL);
    }

    public function testDefaultPageSizeIs10000(): void
    {
        self::assertSame(10000, Versions::DEFAULT_PAGE_SIZE);
    }

    public function testVersionNamesCoverAllMajors(): void
    {
        $names = Versions::names();

        // PHP casts canonical numeric string keys to int, so normalize to
        // strings for a set comparison matching the Python test.
        $keys = \array_map('strval', \array_keys($names));

        self::assertSame(
            ['20', '21', '22', '26', 'future'],
            $keys,
        );
        self::assertSame('Claris FileMaker 2026', $names['26']);
        self::assertSame('Claris FileMaker 2025', $names['22']);
    }

    public function testMatrixHasAllVersions(): void
    {
        $keys = \array_map('strval', \array_keys(Versions::matrix()));

        self::assertSame(
            ['20', '21', '22', '26', 'future'],
            $keys,
        );
    }

    #[DataProvider('hasFeatureProvider')]
    public function testHasFeature(string $version, string $feature, bool $expected): void
    {
        self::assertSame($expected, Versions::hasFeature($version, $feature));
    }

    /**
     * @return array<string, array{string, string, bool}>
     */
    public static function hasFeatureProvider(): array
    {
        return [
            '20 webhooks false' => ['20', 'webhooks', false],
            '22 webhooks true' => ['22', 'webhooks', true],
            '26 scriptsByFmsid true' => ['26', 'scriptsByFmsid', true],
            '20 scriptsByFmsid false' => ['20', 'scriptsByFmsid', false],
            '21 scriptsByFmsid true' => ['21', 'scriptsByFmsid', true],
            '26 authBasic true' => ['26', 'authBasic', true],
            '20 authOAuth false' => ['20', 'authOAuth', false],
            '21 authOAuth true' => ['21', 'authOAuth', true],
            'future aiAnnotation true' => ['future', 'aiAnnotation', true],
            '20 fmComment false' => ['20', 'fmComment', false],
            '21 fmComment true' => ['21', 'fmComment', true],
            '20 metadataFiltering false' => ['20', 'metadataFiltering', false],
            '22 metadataFiltering true' => ['22', 'metadataFiltering', true],
            '26 computedAnnotation true' => ['26', 'computedAnnotation', true],
            '22 computedAnnotation false' => ['22', 'computedAnnotation', false],
        ];
    }

    public function testHasFeatureUnknownVersionReturnsFalse(): void
    {
        self::assertFalse(Versions::hasFeature('99', 'webhooks'));
    }

    public function testHasFeatureUnknownFeatureReturnsFalse(): void
    {
        self::assertFalse(Versions::hasFeature('26', 'nonexistentFeature'));
    }

    #[DataProvider('hasQueryOptionProvider')]
    public function testHasQueryOption(string $version, string $option, bool $expected): void
    {
        self::assertSame($expected, Versions::hasQueryOption($version, $option));
    }

    /**
     * @return array<string, array{string, string, bool}>
     */
    public static function hasQueryOptionProvider(): array
    {
        return [
            '20 apply false' => ['20', 'apply', false],
            '22 apply true' => ['22', 'apply', true],
            '26 filter true' => ['26', 'filter', true],
            '20 search false' => ['20', 'search', false],
            '26 compute false' => ['26', 'compute', false],
        ];
    }

    public function testMinVersionForFeature(): void
    {
        self::assertSame('22', Versions::minVersionForFeature('webhooks'));
        self::assertSame('21', Versions::minVersionForFeature('scriptsByFmsid'));
        self::assertSame('20', Versions::minVersionForFeature('authBasic'));
        self::assertSame('21', Versions::minVersionForFeature('aiAnnotation'));
        self::assertSame('26', Versions::minVersionForFeature('computedAnnotation'));
        self::assertSame('22', Versions::minVersionForFeature('metadataFiltering'));
    }

    public function testMinVersionForUnknownFeatureReturnsNull(): void
    {
        self::assertNull(Versions::minVersionForFeature('nonexistent'));
    }

    public function testVersionInfoFields(): void
    {
        $matrix = Versions::matrix();
        $info = $matrix['26'] ?? null;
        self::assertInstanceOf(FMVersionInfo::class, $info);

        self::assertSame('26', $info->major->value);
        self::assertSame('Claris FileMaker 2026', $info->name);
        self::assertSame(2026, $info->releaseYear);
        self::assertSame('current', $info->status->value);
        self::assertSame('4.01', $info->odataProtocolVersion->value);
        self::assertTrue($info->features->has('scriptsByFmsid'));
        self::assertTrue($info->queryOptions->has('apply'));
    }

    public function testV20UsesOData40(): void
    {
        $matrix = Versions::matrix();
        $info = $matrix['20'] ?? null;
        self::assertInstanceOf(FMVersionInfo::class, $info);
        self::assertSame('4.0', $info->odataProtocolVersion->value);
    }

    public function testV21UsesOData401(): void
    {
        $matrix = Versions::matrix();
        $info = $matrix['21'] ?? null;
        self::assertInstanceOf(FMVersionInfo::class, $info);
        self::assertSame('4.01', $info->odataProtocolVersion->value);
    }
}
