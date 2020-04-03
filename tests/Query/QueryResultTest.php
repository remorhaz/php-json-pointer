<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Query;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Locator\ReferenceInterface;
use Remorhaz\JSON\Pointer\Query\Exception\LastReferenceNotFoundException;
use Remorhaz\JSON\Pointer\Query\Exception\ParentNotFoundException;
use Remorhaz\JSON\Pointer\Query\Exception\SelectionNotFoundException;
use Remorhaz\JSON\Pointer\Query\QueryResult;

/**
 * @covers \Remorhaz\JSON\Pointer\Query\QueryResult
 */
class QueryResultTest extends TestCase
{

    public function testGetSource_ConstructedWithSource_ReturnsSameInstance(): void
    {
        $result = new QueryResult('a');
        self::assertSame('a', $result->getSource());
    }

    public function testHasSelection_ConstructedWithoutSelection_ReturnsFalse(): void
    {
        $result = new QueryResult('a');
        self::assertFalse($result->hasSelection());
    }

    public function testHasSelection_ConstructedWithSelection_ReturnsFalse(): void
    {
        $result = new QueryResult(
            'a',
            $this->createMock(NodeValueInterface::class)
        );
        self::assertTrue($result->hasSelection());
    }

    public function testGetSelection_ConstructedWithoutSelection_ThrowsException(): void
    {
        $result = new QueryResult('a');
        $this->expectException(SelectionNotFoundException::class);
        $result->getSelection();
    }

    public function testGetSelection_ConstructedWithSelection_ReturnsSameInstance(): void
    {
        $selection = $this->createMock(NodeValueInterface::class);
        $result = new QueryResult('a', $selection);
        self::assertSame($selection, $result->getSelection());
    }

    public function testHasParent_ConstructedWithoutParent_ReturnsFalse(): void
    {
        $result = new QueryResult('a');
        self::assertFalse($result->hasParent());
    }

    public function testHasParent_ConstructedWithParent_ReturnsTrue(): void
    {
        $parent = $this->createMock(NodeValueInterface::class);
        $result = new QueryResult('a', null, $parent);
        self::assertTrue($result->hasParent());
    }

    public function testGetParent_ConstructedWithoutParent_ThrowsException(): void
    {
        $result = new QueryResult('a');
        $this->expectException(ParentNotFoundException::class);
        $result->getParent();
    }

    public function testGetParent_ConstructedWithParent_ReturnsSameInstance(): void
    {
        $parent = $this->createMock(NodeValueInterface::class);
        $result = new QueryResult('a', null, $parent);
        self::assertSame($parent, $result->getParent());
    }

    public function testHasLastReference_ConstructedWithoutLastReference_ReturnsFalse(): void
    {
        $result = new QueryResult('a');
        self::assertFalse($result->hasLastReference());
    }

    public function testHasLastReference_ConstructedWithLastReference_ReturnsTrue(): void
    {
        $reference = $this->createMock(ReferenceInterface::class);
        $result = new QueryResult('a', null, null, $reference);
        self::assertTrue($result->hasLastReference());
    }

    public function testGetLastReference_ConstructedWithoutLastReference_ThrowsException(): void
    {
        $result = new QueryResult('a');
        $this->expectException(LastReferenceNotFoundException::class);
        $result->getLastReference();
    }

    public function testGetLastReference_ConstructedWithLastReference_ReturnsSameInstance(): void
    {
        $reference = $this->createMock(ReferenceInterface::class);
        $result = new QueryResult('a', null, null, $reference);
        self::assertSame($reference, $result->getLastReference());
    }
}
