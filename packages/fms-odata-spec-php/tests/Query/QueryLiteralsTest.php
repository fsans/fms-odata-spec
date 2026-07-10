<?php

declare(strict_types=1);

namespace FmsOData\Spec\Tests\Query;

use FmsOData\Spec\Query\AggregateExpression;
use FmsOData\Spec\Query\AggregateFunction;
use FmsOData\Spec\Query\AggregateTransformation;
use FmsOData\Spec\Query\ApplyTransformation;
use FmsOData\Spec\Query\GroupByExpression;
use FmsOData\Spec\Query\GroupByTransformation;
use FmsOData\Spec\Query\ODataCollection;
use FmsOData\Spec\Query\ODataEntity;
use FmsOData\Spec\Query\OrderByClause;
use FmsOData\Spec\Query\QueryLiterals;
use FmsOData\Spec\Query\QueryParams;
use FmsOData\Spec\Query\QueryResult;
use FmsOData\Spec\Query\SortDirection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for the Query domain literal helpers and DTOs, ported from the
 * Python test_query_options.py.
 *
 * Covers string-literal escaping, primitive/datetime literal formatting,
 * and construction of the $apply, query-param, and OData response DTOs.
 */
final class QueryLiteralsTest extends TestCase
{
    public function testEscapeStringLiteralDoublesQuotes(): void
    {
        self::assertSame("O''Brien", QueryLiterals::escapeStringLiteral("O'Brien"));
        self::assertSame('plain', QueryLiterals::escapeStringLiteral('plain'));
        self::assertSame("a''b''c", QueryLiterals::escapeStringLiteral("a'b'c"));
    }

    /**
     * @param string|int|float|bool $value
     */
    #[DataProvider('primitiveLiteralProvider')]
    public function testFormatLiteralPrimitives(string|int|float|bool $value, string $expected): void
    {
        self::assertSame($expected, QueryLiterals::formatLiteral($value));
    }

    /**
     * @return array<string, array{string|int|float|bool, string}>
     */
    public static function primitiveLiteralProvider(): array
    {
        return [
            'string' => ['hello', "'hello'"],
            'string with quote' => ["O'Brien", "'O''Brien'"],
            'int' => [42, '42'],
            'float' => [3.14, '3.14'],
            'true' => [true, 'true'],
            'false' => [false, 'false'],
        ];
    }

    public function testFormatLiteralDatetimeAwareStripsMicroseconds(): void
    {
        $dt = new \DateTime('2026-06-30 12:34:56.789000', new \DateTimeZone('UTC'));

        self::assertSame('2026-06-30T12:34:56Z', QueryLiterals::formatLiteral($dt));
    }

    public function testFormatLiteralDatetimeNaive(): void
    {
        // PHP's DateTime always carries a timezone (defaults to system tz).
        // Unlike Python's naive datetime, PHP has no "no timezone" case.
        // A DateTime without explicit timezone uses the system default and
        // gets the corresponding offset suffix (or 'Z' if system tz is UTC).
        $dt = new \DateTime('2026-06-30 12:34:56.789000');
        $tz = $dt->getTimezone();
        $offset = $tz->getOffset($dt);

        if ($offset === 0) {
            $expected = '2026-06-30T12:34:56Z';
        } else {
            $sign = $offset >= 0 ? '+' : '-';
            $expected = \sprintf(
                '2026-06-30T12:34:56%s%02d:%02d',
                $sign,
                (int) (\abs($offset) / 3600),
                (int) (\abs($offset) / 60 % 60),
            );
        }

        self::assertSame($expected, QueryLiterals::formatLiteral($dt));
    }

    public function testFormatLiteralDatetimeNoMicros(): void
    {
        $dt = new \DateTime('2026-06-30 12:34:56', new \DateTimeZone('UTC'));

        self::assertSame('2026-06-30T12:34:56Z', QueryLiterals::formatLiteral($dt));
    }

    public function testOrderbyClauseDefaults(): void
    {
        $clause = new OrderByClause('name');

        self::assertSame('name', $clause->field);
        self::assertNull($clause->direction);
    }

    public function testOrderbyClauseWithDirection(): void
    {
        $clause = new OrderByClause('name', SortDirection::DESC);

        self::assertSame(SortDirection::DESC, $clause->direction);
    }

    public function testAggregateExpressionConstructs(): void
    {
        $expr = new AggregateExpression('price', AggregateFunction::SUM, 'total');

        self::assertSame('price', $expr->field);
        self::assertSame(AggregateFunction::SUM, $expr->function);
        self::assertSame('total', $expr->alias);
        self::assertNull($expr->add);
    }

    public function testGroupbyExpressionConstructs(): void
    {
        $expr = new GroupByExpression(['region'], null);

        self::assertSame(['region'], $expr->fields);
        self::assertNull($expr->aggregate);
    }

    public function testAggregateTransformationDiscriminator(): void
    {
        $transformation = new AggregateTransformation([
            new AggregateExpression('x', AggregateFunction::MIN, 'mn'),
        ]);

        self::assertInstanceOf(ApplyTransformation::class, $transformation);
        self::assertSame('aggregate', $transformation->type());
        self::assertCount(1, $transformation->expressions);
    }

    public function testGroupbyTransformationDiscriminator(): void
    {
        $transformation = new GroupByTransformation(new GroupByExpression(['a']));

        self::assertInstanceOf(ApplyTransformation::class, $transformation);
        self::assertSame('groupby', $transformation->type());
        self::assertSame(['a'], $transformation->expression->fields);
    }

    public function testQueryParamsDefaultsAllNull(): void
    {
        $params = new QueryParams();

        self::assertNull($params->filter);
        self::assertNull($params->select);
        self::assertNull($params->orderby);
        self::assertNull($params->top);
        self::assertNull($params->skip);
        self::assertNull($params->expand);
        self::assertNull($params->count);
        self::assertNull($params->apply);
    }

    public function testQueryParamsWithValues(): void
    {
        $params = new QueryParams(
            filter: "name eq 'x'",
            select: ['a', 'b'],
            top: 10,
            skip: 5,
            expand: ['rel'],
            count: true,
        );

        self::assertSame("name eq 'x'", $params->filter);
        self::assertSame(['a', 'b'], $params->select);
        self::assertSame(10, $params->top);
        self::assertSame(5, $params->skip);
        self::assertSame(['rel'], $params->expand);
        self::assertTrue($params->count);
    }

    public function testOdataCollectionConstructs(): void
    {
        $collection = new ODataCollection('ctx', [1, 2, 3], odataCount: 3);

        self::assertSame([1, 2, 3], $collection->value);
        self::assertSame(3, $collection->odataCount);
        self::assertNull($collection->odataNextLink);
    }

    public function testOdataEntityWrapsT(): void
    {
        $entity = new ODataEntity('ctx', ['id' => 1, 'name' => 'x'], 'etag');

        self::assertSame(['id' => 1, 'name' => 'x'], $entity->entity);
        self::assertSame('etag', $entity->odataEtag);
        self::assertSame(1, $entity->entity['id']);
    }

    public function testQueryResultConstructs(): void
    {
        $result = new QueryResult([1, 2], 2, 'link');

        self::assertSame([1, 2], $result->value);
        self::assertSame(2, $result->count);
        self::assertSame('link', $result->nextLink);
    }

    public function testFormatLiteralDatetimeNegativeTzWithMicros(): void
    {
        $dt = new \DateTime('2026-06-30 12:34:56.789000', new \DateTimeZone('-0500'));

        self::assertSame('2026-06-30T12:34:56-05:00', QueryLiterals::formatLiteral($dt));
    }

    public function testFormatLiteralDatetimeNegativeTzNoMicros(): void
    {
        $dt = new \DateTime('2026-06-30 12:34:56', new \DateTimeZone('-0500'));

        self::assertSame('2026-06-30T12:34:56-05:00', QueryLiterals::formatLiteral($dt));
    }
}
