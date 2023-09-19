<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Parser\ParserInterface;

/**
 * @covers \Remorhaz\JSON\Pointer\Query\LazyQuery
 */
class LazyQueryTest extends TestCase
{
    public function testGetSource_ConstructedWithSource_ReturnsSameValue(): void
    {
        $query = new LazyQuery(
            'a',
            $this->createMock(ParserInterface::class),
        );
        self::assertSame('a', $query->getSource());
    }

    public function testInvoke_CalledTwice_InvokesParserOnce(): void
    {
        $parser = $this->createMock(ParserInterface::class);
        $query = new LazyQuery('', $parser);
        $value = $this->createMock(NodeValueInterface::class);
        $parser
            ->expects(self::once())
            ->method('buildLocator');
        $query($value);
        $query($value);
    }

    public function testInvoke_ConstructedWithSource_ParserSameSource(): void
    {
        $parser = $this->createMock(ParserInterface::class);
        $query = new LazyQuery('a', $parser);
        $value = $this->createMock(NodeValueInterface::class);
        $parser
            ->expects(self::once())
            ->method('buildLocator')
            ->with('a');
        $query($value);
    }
}
