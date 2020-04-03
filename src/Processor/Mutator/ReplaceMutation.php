<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Mutator;

use Generator;
use Iterator;
use Remorhaz\JSON\Data\Event\AfterElementEventInterface;
use Remorhaz\JSON\Data\Event\AfterPropertyEventInterface;
use Remorhaz\JSON\Data\Event\BeforeArrayEventInterface;
use Remorhaz\JSON\Data\Event\BeforeElementEventInterface;
use Remorhaz\JSON\Data\Event\BeforeObjectEventInterface;
use Remorhaz\JSON\Data\Event\BeforePropertyEventInterface;
use Remorhaz\JSON\Data\Event\EventInterface;
use Remorhaz\JSON\Data\Event\ScalarEventInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;

final class ReplaceMutation implements MutationInterface
{

    private $newNode;

    private $path;

    public function __construct(NodeValueInterface $newNode, PathInterface $path)
    {
        $this->newNode = $newNode;
        $this->path = $path;
    }

    public function __invoke(EventInterface $event, ValueWalkerInterface $valueWalker): Iterator
    {
        return $this->createEventGenerator($event, $valueWalker);
    }

    public function reset(): void
    {
    }

    private function createEventGenerator(EventInterface $event, ValueWalkerInterface $valueWalker): Generator
    {
        if ($this->path->equals($event->getPath())) {
            yield from $this->createReplaceEventGenerator($event, $valueWalker);

            return;
        }
        if (!$this->path->contains($event->getPath())) {
            yield $event;
        }
    }

    private function createReplaceEventGenerator(EventInterface $event, ValueWalkerInterface $valueWalker): Generator
    {
        switch (true) {
            case $event instanceof BeforeElementEventInterface:
            case $event instanceof BeforePropertyEventInterface:
            case $event instanceof AfterElementEventInterface:
            case $event instanceof AfterPropertyEventInterface:
                yield $event;
                break;

            case $event instanceof ScalarEventInterface:
            case $event instanceof BeforeArrayEventInterface:
            case $event instanceof BeforeObjectEventInterface:
                yield from $valueWalker->createEventIterator($this->newNode, $event->getPath());
                break;
        }
    }
}
