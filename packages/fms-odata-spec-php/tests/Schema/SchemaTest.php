<?php

declare(strict_types=1);

namespace FmsOData\Spec\Tests\Schema;

use FmsOData\Spec\Schema\AddFieldsParams;
use FmsOData\Spec\Schema\CreateTableParams;
use FmsOData\Spec\Schema\FMFieldDefinition;
use FmsOData\Spec\Schema\FMFieldType;
use FmsOData\Spec\Schema\ParsedFieldType;
use FmsOData\Spec\Schema\Schema;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class SchemaTest extends TestCase
{
    public function test_field_types_complete(): void
    {
        $all = FMFieldType::all();
        $values = \array_map(fn (FMFieldType $t): string => $t->value, $all);
        self::assertContains('VARCHAR', $values);
        self::assertContains('BINARY VARYING', $values);
        self::assertCount(12, $all);
    }

    public function test_field_defaults_complete(): void
    {
        self::assertContains('CURRENT_TIMESTAMP', Schema::FIELD_DEFAULTS);
        self::assertContains('USER', Schema::FIELD_DEFAULTS);
    }

    public function test_field_definition_defaults(): void
    {
        $f = new FMFieldDefinition(name: 'id', type: 'INT');
        self::assertFalse($f->primary);
        self::assertFalse($f->unique);
        self::assertTrue($f->nullable);
        self::assertFalse($f->global);
        self::assertNull($f->default);
    }

    public function test_field_definition_to_odata_array_uses_global_key(): void
    {
        $f = new FMFieldDefinition(name: 'g', type: 'INT', global: true, default: 'USER');
        $d = $f->toODataArray();
        self::assertTrue($d['global']);
        self::assertSame('USER', $d['default']);
        self::assertArrayNotHasKey('externalSecurePath', $d);
    }

    public function test_field_definition_to_odata_array_with_external_path(): void
    {
        $f = new FMFieldDefinition(
            name: 'pic',
            type: 'BLOB',
            externalSecurePath: 'secure/storage',
        );
        $d = $f->toODataArray();
        self::assertSame('secure/storage', $d['externalSecurePath']);
    }

    public function test_create_table_params_to_odata_array(): void
    {
        $p = new CreateTableParams(
            tableName: 'T',
            fields: [new FMFieldDefinition(name: 'id', type: 'INT', primary: true)],
        );
        $d = $p->toODataArray();
        self::assertSame('T', $d['tableName'] ?? null);
        /** @var array<int, array<string, mixed>> $fields */
        $fields = $d['fields'] ?? [];
        self::assertSame('id', $fields[0]['name'] ?? null);
        self::assertTrue($fields[0]['primary'] ?? false);
    }

    public function test_add_fields_params_to_odata_array(): void
    {
        $p = new AddFieldsParams(tableName: 'T', fields: []);
        self::assertSame(['tableName' => 'T', 'fields' => []], $p->toODataArray());
    }

    #[DataProvider('parseFieldTypeProvider')]
    public function test_parse_field_type(string $typeStr, ParsedFieldType $expected): void
    {
        $result = Schema::parseFieldType($typeStr);
        self::assertSame($expected->baseType, $result->baseType);
        self::assertSame($expected->length, $result->length);
        self::assertSame($expected->repetitions, $result->repetitions);
    }

    /**
     * @return \Generator<string, array{string, ParsedFieldType}>
     */
    public static function parseFieldTypeProvider(): \Generator
    {
        yield 'varchar with length' => ['VARCHAR(200)', new ParsedFieldType('VARCHAR', 200)];
        yield 'int with repetitions' => ['INT[4]', new ParsedFieldType('INT', repetitions: 4)];
        yield 'varchar with both' => ['VARCHAR(200)[4]', new ParsedFieldType('VARCHAR', 200, 4)];
        yield 'blob plain' => ['BLOB', new ParsedFieldType('BLOB')];
    }
}
