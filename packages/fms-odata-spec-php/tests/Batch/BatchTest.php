<?php

declare(strict_types=1);

namespace FmsOData\Spec\Tests\Batch;

use FmsOData\Spec\Batch\Batch;
use FmsOData\Spec\Batch\BatchHandle;
use FmsOData\Spec\Batch\BatchOpResult;
use FmsOData\Spec\Batch\BatchOpType;
use FmsOData\Spec\Batch\BatchOperation;
use FmsOData\Spec\Batch\BatchRequest;
use FmsOData\Spec\Batch\BatchResult;
use FmsOData\Spec\Batch\Changeset;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Batch domain, ported from test_batch.py.
 */
final class BatchTest extends TestCase
{
    public function testGenerateBoundaryHasPrefix(): void
    {
        $boundary = Batch::generateBoundary();

        self::assertStringStartsWith('batch_', $boundary);
        // prefix ('batch') + '_' + 32 hex chars
        self::assertSame(6 + 32, \strlen($boundary));

        $custom = Batch::generateBoundary('custom');

        self::assertStringStartsWith('custom_', $custom);
    }

    public function testGenerateBoundaryIsUnique(): void
    {
        $boundaries = [];

        for ($i = 0; $i < 100; $i++) {
            $boundaries[] = Batch::generateBoundary();
        }

        self::assertSame(100, \count(\array_unique($boundaries)));
    }

    public function testBatchOperationDefaults(): void
    {
        $operation = new BatchOperation(BatchOpType::LIST, 'Customers');

        self::assertSame(BatchOpType::LIST, $operation->op);
        self::assertSame('Customers', $operation->entitySet);
        self::assertNull($operation->key);
        self::assertNull($operation->body);
        self::assertNull($operation->query);
        self::assertNull($operation->contentId);
    }

    public function testBatchOperationWithAllFields(): void
    {
        $operation = new BatchOperation(
            BatchOpType::CREATE,
            'Customers',
            body: ['name' => 'x'],
            contentId: 1,
        );

        self::assertSame(['name' => 'x'], $operation->body);
        self::assertSame(1, $operation->contentId);
    }

    public function testChangesetConstructs(): void
    {
        $changeset = new Changeset([new BatchOperation(BatchOpType::CREATE, 'T')]);

        self::assertCount(1, $changeset->operations);
    }

    public function testBatchRequestDefaultsEmpty(): void
    {
        $request = new BatchRequest();

        self::assertSame([], $request->retrieveOps);
        self::assertSame([], $request->changesets);
    }

    public function testBatchOpResultConstructs(): void
    {
        $result = new BatchOpResult(200, ['X' => 'y'], true, ['k' => 1]);

        self::assertTrue($result->ok);
        self::assertSame(['k' => 1], $result->body);
    }

    public function testBatchResultDefaults(): void
    {
        $result = new BatchResult();

        self::assertSame([], $result->responses);
        self::assertFalse($result->ok);
    }

    public function testBatchHandleHoldsValue(): void
    {
        $handle = new BatchHandle('test');

        self::assertSame('test', $handle->handle);
    }
}
