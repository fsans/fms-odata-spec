<?php

declare(strict_types=1);

namespace FmsOData\Spec\Tests\Metadata;

use FmsOData\Spec\Metadata\EdmAction;
use FmsOData\Spec\Metadata\EdmEntitySet;
use FmsOData\Spec\Metadata\EdmEntityType;
use FmsOData\Spec\Metadata\EdmEnumMember;
use FmsOData\Spec\Metadata\EdmEnumType;
use FmsOData\Spec\Metadata\EdmProperty;
use FmsOData\Spec\Metadata\FMAnnotations;
use FmsOData\Spec\Metadata\FMServerVersion;
use FmsOData\Spec\Metadata\Metadata;
use FmsOData\Spec\Metadata\ODataMetadata;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for the Metadata domain, ported from the Python test_metadata.py.
 *
 * Covers the system field/table constants, the four server-version detection
 * strategies, the major-version extraction helpers, and construction of the
 * Edm/ODataMetadata DTOs.
 */
final class MetadataTest extends TestCase
{
    public function testSystemFields(): void
    {
        self::assertSame('ROWID', Metadata::SYSTEM_FIELDS['ROWID']);
        self::assertSame('ROWMODID', Metadata::SYSTEM_FIELDS['ROWMODID']);
    }

    public function testSystemTables(): void
    {
        self::assertSame('FileMaker_Tables', Metadata::SYSTEM_TABLES['TABLES']);
        self::assertSame('FileMaker_Fields', Metadata::SYSTEM_TABLES['FIELDS']);
        self::assertSame('FileMaker_Indexes', Metadata::SYSTEM_TABLES['INDEXES']);
    }

    #[DataProvider('versionStringProvider')]
    public function testParseVersionString(string $raw, ?FMServerVersion $expected): void
    {
        $result = Metadata::parseVersionString($raw);

        if ($expected === null) {
            self::assertNull($result);
        } else {
            self::assertNotNull($result);
            self::assertSame($expected->major, $result->major);
            self::assertSame($expected->minor, $result->minor);
            self::assertSame($expected->patch, $result->patch);
            self::assertSame($expected->raw, $result->raw);
        }
    }

    /**
     * @return array<string, array{string, FMServerVersion|null}>
     */
    public static function versionStringProvider(): array
    {
        return [
            'four-part' => ['21.1.2.500', new FMServerVersion(21, 1, 2, '21.1.2.500')],
            'three-part' => ['26.0.1', new FMServerVersion(26, 0, 1, '26.0.1')],
            'padded' => ['  20.2.1  ', new FMServerVersion(20, 2, 1, '20.2.1')],
            'non-version' => ['nope', null],
            'empty' => ['', null],
        ];
    }

    public function testParseServerVersionStrategy1a(): void
    {
        $xml = '<Annotation Term="Org.OData.Core.V1.ProductVersion" String="26.0.1.500"/>';
        $result = Metadata::parseServerVersion($xml);

        self::assertNotNull($result);
        self::assertSame(26, $result->major);
        self::assertSame(0, $result->minor);
        self::assertSame(1, $result->patch);
    }

    public function testParseServerVersionStrategy1bReversedAttrs(): void
    {
        $xml = '<Annotation String="21.1.2" Term="Org.OData.Core.V1.ProductVersion"/>';
        $result = Metadata::parseServerVersion($xml);

        self::assertNotNull($result);
        self::assertSame(21, $result->major);
    }

    public function testParseServerVersionStrategy1cServerVersion(): void
    {
        $xml = '<Annotation Term="ServerVersion" String="OData Engine 26.0.1"/>';
        $result = Metadata::parseServerVersion($xml);

        self::assertNotNull($result);
        self::assertSame(26, $result->major);
    }

    public function testParseServerVersionStrategy1dReversed(): void
    {
        $xml = '<Annotation String="OData Engine 26.0.1" Term="ServerVersion"/>';
        $result = Metadata::parseServerVersion($xml);

        self::assertNotNull($result);
        self::assertSame(26, $result->major);
    }

    public function testParseServerVersionStrategy2GenericFallback(): void
    {
        $xml = '<Annotation Term="MyVersion" String="anything 22.3.1 here"/>';
        $result = Metadata::parseServerVersion($xml);

        self::assertNotNull($result);
        self::assertSame(22, $result->major);
    }

    public function testParseServerVersionIgnoresOdata40(): void
    {
        $xml = '<Annotation Term="Org.OData.Core.V1.Version" String="4.0"/>';
        $result = Metadata::parseServerVersion($xml);

        self::assertNull($result);
    }

    public function testParseServerVersionEmptyOrNone(): void
    {
        self::assertNull(Metadata::parseServerVersion(''));
        self::assertNull(Metadata::parseServerVersion(null));
    }

    public function testParseServerVersionNoMatch(): void
    {
        self::assertNull(Metadata::parseServerVersion('<foo>bar</foo>'));
    }

    public function testExtractMajorVersion(): void
    {
        self::assertSame('26', Metadata::extractMajorVersion('26.0.1.500'));
        self::assertNull(Metadata::extractMajorVersion(null));
        self::assertNull(Metadata::extractMajorVersion('nope'));
    }

    public function testExtractMajorVersionFromMetadata(): void
    {
        $xml = '<Annotation Term="Org.OData.Core.V1.ProductVersion" String="22.1.0"/>';

        self::assertSame('22', Metadata::extractMajorVersionFromMetadata($xml));
        self::assertNull(Metadata::extractMajorVersionFromMetadata('<foo/>'));
    }

    public function testEdmPropertyConstructs(): void
    {
        $property = new EdmProperty('id', 'Edm.Int32', isKey: true);

        self::assertSame('id', $property->name);
        self::assertTrue($property->isKey);
        self::assertNull($property->nullable);
    }

    public function testEdmEntityTypeConstructs(): void
    {
        $entityType = new EdmEntityType('Customer', ['id'], []);

        self::assertSame(['id'], $entityType->keys);
        self::assertSame([], $entityType->properties);
    }

    public function testEdmEntitySetConstructs(): void
    {
        $entitySet = new EdmEntitySet('Customers', 'NS.Customer');

        self::assertSame('NS.Customer', $entitySet->entityType);
    }

    public function testEdmActionConstructs(): void
    {
        $action = new EdmAction('MyScript', false);

        self::assertFalse($action->isBound);
        self::assertNull($action->scriptId);
    }

    public function testEdmEnumTypeConstructs(): void
    {
        $enumType = new EdmEnumType('Status', [
            new EdmEnumMember('open', 1),
            new EdmEnumMember('closed', '2'),
        ]);

        self::assertCount(2, $enumType->members);
        self::assertSame(1, $enumType->members[0]->value);
    }

    public function testFmAnnotationsConstructs(): void
    {
        $annotations = new FMAnnotations(productVersion: '26.0.1');

        self::assertSame('26.0.1', $annotations->productVersion);
        self::assertNull($annotations->global);
        self::assertNull($annotations->autoGenerated);
    }

    public function testOdataMetadataConstructs(): void
    {
        $metadata = new ODataMetadata('NS', [], [], [], [], '<xml/>');

        self::assertSame('NS', $metadata->namespace);
        self::assertSame('<xml/>', $metadata->raw);
        self::assertNull($metadata->productVersion);
    }
}
