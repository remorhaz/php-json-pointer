<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Mutator;

use ArrayIterator;
use Iterator;
use Remorhaz\JSON\Data\Event\AfterElementEvent;
use Remorhaz\JSON\Data\Event\AfterElementEventInterface;
use Remorhaz\JSON\Data\Event\BeforeElementEvent;
use Remorhaz\JSON\Data\Event\BeforeElementEventInterface;
use Remorhaz\JSON\Data\Event\EventInterface;
use Remorhaz\JSON\Data\Export\EventDecoder;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;

final class DeleteElementMutation implements MutationInterface
{
    private ?int $elementIndex = null;

    private ?int $elementCounter = null;

    private EventDecoder $eventDecoder;

    public function __construct(
        private PathInterface $arrayPath,
        private PathInterface $elementPath,
    ) {
        $this->eventDecoder = new EventDecoder();
    }

    public function __invoke(EventInterface $event, ValueWalkerInterface $valueWalker): Iterator
    {
        if ($this->elementPath->equals($event->getPath())) {
            if ($event instanceof AfterElementEventInterface) {
                $this->elementCounter = $event->getIndex();
            }
        }
        if ($this->elementPath->contains($event->getPath())) {
            return;
        }
        if (!$this->parentPathMatches($event)) {
            yield $event;

            return;
        }

        if (!isset($this->elementCounter)) {
            yield $event;

            return;
        }

        switch (true) {
            case $event instanceof BeforeElementEventInterface:
                yield new BeforeElementEvent(
                    $this->elementCounter,
                    $this
                        ->arrayPath
                        ->copyWithElement($this->elementCounter)
                );

                return;

            case $event instanceof AfterElementEventInterface:
                yield new AfterElementEvent(
                    $this->elementCounter,
                    $this
                        ->arrayPath
                        ->copyWithElement($this->elementCounter)
                );
                $this->elementCounter++;

                return;

            default:
                yield from $valueWalker->createEventIterator(
                    $this
                        ->eventDecoder
                        ->exportExistingEvents(new ArrayIterator([$event])),
                    $this
                        ->arrayPath
                        ->copyWithElement($this->elementCounter)
                );
        }

        return;
    }

    public function reset(): void
    {
        unset($this->elementIndex, $this->elementCounter);
    }

    private function parentPathMatches(EventInterface $event): bool
    {
        $pathElements = $event->getPath()->getElements();

        return !empty($pathElements) && $event
            ->getPath()
            ->copyParent()
            ->equals($this->arrayPath);
    }
}
