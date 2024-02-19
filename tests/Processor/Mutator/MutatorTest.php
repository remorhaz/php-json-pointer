<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Processor\Mutator;

use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\EventDecoderInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;
use Remorhaz\JSON\Pointer\Processor\Mutator\Mutator;

#[CoversClass(Mutator::class)]
class MutatorTest extends TestCase
{
    public function testMutate_Constructed_DecoderExportsEventsFromWalker(): void
    {
        $valueWalker = self::createStub(ValueWalkerInterface::class);
        $eventDecoder = $this->createMock(EventDecoderInterface::class);
        $mutator = new Mutator($valueWalker, $eventDecoder);

        $events = self::createStub(Iterator::class);
        $valueWalker
            ->method('createMutableEventIterator')
            ->willReturn($events);
        $eventDecoder
            ->expects(self::once())
            ->method('exportEvents')
            ->with($events);
        $mutator->mutate(
            self::createStub(NodeValueInterface::class),
            self::createStub(MutationInterface::class),
        );
    }

    public function testMutate_DecoderReturnsNull_ReturnsNull(): void
    {
        $eventDecoder = self::createStub(EventDecoderInterface::class);
        $mutator = new Mutator(
            self::createStub(ValueWalkerInterface::class),
            $eventDecoder,
        );

        $eventDecoder
            ->method('exportEvents')
            ->willReturn(null);
        $actualValue = $mutator->mutate(
            self::createStub(NodeValueInterface::class),
            self::createStub(MutationInterface::class),
        );
        self::assertNull($actualValue);
    }

    public function testMutate_DecoderReturnsValue_ReturnsSameInstance(): void
    {
        $eventDecoder = self::createStub(EventDecoderInterface::class);
        $mutator = new Mutator(
            self::createStub(ValueWalkerInterface::class),
            $eventDecoder,
        );

        $value = self::createStub(NodeValueInterface::class);
        $eventDecoder
            ->method('exportEvents')
            ->willReturn($value);
        $actualValue = $mutator->mutate(
            self::createStub(NodeValueInterface::class),
            self::createStub(MutationInterface::class),
        );
        self::assertSame($value, $actualValue);
    }

    public function testMutate_GivenRootNodeAndMutation_SameInstancesPassedToValueWalker(): void
    {
        $valueWalker = $this->createMock(ValueWalkerInterface::class);
        $mutator = new Mutator(
            $valueWalker,
            self::createStub(EventDecoderInterface::class),
        );
        $rootValue = self::createStub(NodeValueInterface::class);
        $mutation = self::createStub(MutationInterface::class);

        $valueWalker
            ->expects(self::once())
            ->method('createMutableEventIterator')
            ->with(
                self::identicalTo($rootValue),
                self::anything(),
                self::identicalTo($mutation),
            );
        $mutator->mutate($rootValue, $mutation);
    }

    public function testMutate_ConstructedWithValueWalker_EmptyPathPassedToSameInstance(): void
    {
        $valueWalker = $this->createMock(ValueWalkerInterface::class);
        $mutator = new Mutator(
            $valueWalker,
            self::createStub(EventDecoderInterface::class),
        );

        $valueWalker
            ->expects(self::once())
            ->method('createMutableEventIterator')
            ->with(
                self::anything(),
                self::callback(fn (PathInterface $path): bool => empty($path->getElements())),
                self::anything(),
            );
        $mutator->mutate(
            self::createStub(NodeValueInterface::class),
            self::createStub(MutationInterface::class),
        );
    }
}
