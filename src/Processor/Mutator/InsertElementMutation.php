<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Mutator;

use ArrayIterator;
use Generator;
use Iterator;
use Remorhaz\JSON\Data\Event\AfterElementEvent;
use Remorhaz\JSON\Data\Event\AfterElementEventInterface;
use Remorhaz\JSON\Data\Event\BeforeElementEvent;
use Remorhaz\JSON\Data\Event\BeforeElementEventInterface;
use Remorhaz\JSON\Data\Event\EventInterface;
use Remorhaz\JSON\Data\Export\EventDecoder;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;

final class InsertElementMutation implements MutationInterface
{

    private $value;

    private $path;

    private $elementIndex;

    private $elementCounter = 0;

    private $eventDecoder;

    public function __construct(NodeValueInterface $value, PathInterface $path, int $elementIndex)
    {
        $this->value = $value;
        $this->path = $path;
        $this->elementIndex = $elementIndex;
        $this->eventDecoder = new EventDecoder();
    }

    public function __invoke(EventInterface $event, ValueWalkerInterface $valueWalker): Iterator
    {
        if (!$this->parentPathMatches($event)) {
            yield $event;

            return;
        }

        switch (true) {
            case $event instanceof BeforeElementEventInterface:
                yield from $this->processBeforeElementEvent($event, $valueWalker);
                break;
            case $event instanceof AfterElementEventInterface:
                yield from $this->processAfterElementEvent($event);
                break;

            default:
                yield from $this->processElementValue($event, $valueWalker);
        }
    }

    public function reset(): void
    {
        $this->elementCounter = 0;
    }

    private function parentPathMatches(EventInterface $event): bool
    {
        $pathElements = $event->getPath()->getElements();
        if (empty($pathElements)) {
            return false;
        }

        return $event
            ->getPath()
            ->copyParent()
            ->equals($this->path);
    }

    private function processBeforeElementEvent(
        BeforeElementEventInterface $event,
        ValueWalkerInterface $valueWalker
    ): Generator {
        if ($event->getIndex() < $this->elementIndex) {
            yield $event;

            return;
        }

        if ($event->getIndex() == $this->elementIndex) {
            $elementPath = $this->path->copyWithElement($this->elementIndex);
            yield new BeforeElementEvent($this->elementIndex, $elementPath);
            yield from $valueWalker->createEventIterator($this->value, $elementPath);
            yield new AfterElementEvent($this->elementIndex, $elementPath);
        }
        $shiftedIndex = $event->getIndex() + 1;
        $path = $event
            ->getPath()
            ->copyParent()
            ->copyWithElement($shiftedIndex);
        yield new BeforeElementEvent($shiftedIndex, $path);
    }

    private function processAfterElementEvent(AfterElementEventInterface $event): Generator
    {
        $this->elementCounter++;
        if ($event->getIndex() < $this->elementIndex) {
            yield $event;

            return;
        }

        $shiftedIndex = $event->getIndex() + 1;
        $path = $event
            ->getPath()
            ->copyParent()
            ->copyWithElement($shiftedIndex);
        yield new AfterElementEvent($shiftedIndex, $path);
    }

    private function processElementValue(EventInterface $event, ValueWalkerInterface $valueWalker): Generator
    {
        $path = $event
            ->getPath()
            ->copyParent()
            ->copyWithElement($this->elementCounter + 1);

        yield from $valueWalker->createEventIterator(
            $this
                ->eventDecoder
                ->exportExistingEvents(new ArrayIterator([$event])),
            $path,
        );
    }
}
