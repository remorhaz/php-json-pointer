<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Processor\Mutator;

use Iterator;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\EventDecoderInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;
use Remorhaz\JSON\Pointer\Processor\Mutator\Mutator;

/**
 * @covers \Remorhaz\JSON\Pointer\Processor\Mutator\Mutator
 */
class MutatorTest extends TestCase
{
    public function testMutate_Constructed_DecoderExportsEventsFromWalker(): void
    {
        $valueWalker = $this->createMock(ValueWalkerInterface::class);
        $eventDecoder = $this->createMock(EventDecoderInterface::class);
        $mutator = new Mutator($valueWalker, $eventDecoder);

        $events = $this->createMock(Iterator::class);
        $valueWalker
            ->method('createMutableEventIterator')
            ->willReturn($events);
        $eventDecoder
            ->expects(self::once())
            ->method('exportEvents')
            ->with($events);
        $mutator->mutate(
            $this->createMock(NodeValueInterface::class),
            $this->createMock(MutationInterface::class)
        );
    }

    public function testMutate_DecoderReturnsNull_ReturnsNull(): void
    {
        $eventDecoder = $this->createMock(EventDecoderInterface::class);
        $mutator = new Mutator(
            $this->createMock(ValueWalkerInterface::class),
            $eventDecoder
        );

        $eventDecoder
            ->method('exportEvents')
            ->willReturn(null);
        $actualValue = $mutator->mutate(
            $this->createMock(NodeValueInterface::class),
            $this->createMock(MutationInterface::class)
        );
        self::assertNull($actualValue);
    }

    public function testMutate_DecoderReturnsValue_ReturnsSameInstance(): void
    {
        $eventDecoder = $this->createMock(EventDecoderInterface::class);
        $mutator = new Mutator(
            $this->createMock(ValueWalkerInterface::class),
            $eventDecoder
        );

        $value = $this->createMock(NodeValueInterface::class);
        $eventDecoder
            ->method('exportEvents')
            ->willReturn($value);
        $actualValue = $mutator->mutate(
            $this->createMock(NodeValueInterface::class),
            $this->createMock(MutationInterface::class)
        );
        self::assertSame($value, $actualValue);
    }

    public function testMutate_GivenRootNodeAndMutation_SameInstancesPassedToValueWalker(): void
    {
        $valueWalker = $this->createMock(ValueWalkerInterface::class);
        $mutator = new Mutator(
            $valueWalker,
            $this->createMock(EventDecoderInterface::class)
        );
        $rootValue = $this->createMock(NodeValueInterface::class);
        $mutation = $this->createMock(MutationInterface::class);

        $valueWalker
            ->expects(self::once())
            ->method('createMutableEventIterator')
            ->with(
                self::identicalTo($rootValue),
                self::anything(),
                self::identicalTo($mutation)
            );
        $mutator->mutate($rootValue, $mutation);
    }

    public function testMutate_ConstructedWithValueWalker_EmptyPathPassedToSameInstance(): void
    {
        $valueWalker = $this->createMock(ValueWalkerInterface::class);
        $mutator = new Mutator(
            $valueWalker,
            $this->createMock(EventDecoderInterface::class)
        );

        $valueWalker
            ->expects(self::once())
            ->method('createMutableEventIterator')
            ->with(
                self::anything(),
                self::callback(
                    function (PathInterface $path): bool {
                        return empty($path->getElements());
                    }
                ),
                self::anything()
            );
        $mutator->mutate(
            $this->createMock(NodeValueInterface::class),
            $this->createMock(MutationInterface::class)
        );
    }
}
