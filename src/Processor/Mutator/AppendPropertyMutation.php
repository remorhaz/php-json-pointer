<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Mutator;

use Iterator;
use Remorhaz\JSON\Data\Event\AfterObjectEventInterface;
use Remorhaz\JSON\Data\Event\AfterPropertyEvent;
use Remorhaz\JSON\Data\Event\BeforePropertyEvent;
use Remorhaz\JSON\Data\Event\EventInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;

final class AppendPropertyMutation implements MutationInterface
{
    public function __construct(
        private readonly NodeValueInterface $value,
        private readonly PathInterface $path,
        private readonly string $propertyName,
    ) {
    }

    public function __invoke(EventInterface $event, ValueWalkerInterface $valueWalker): Iterator
    {
        return $this->createEventGenerator($event, $valueWalker);
    }

    public function reset(): void
    {
    }

    private function createEventGenerator(EventInterface $event, ValueWalkerInterface $valueWalker): Iterator
    {
        if (!$this->path->equals($event->getPath())) {
            yield $event;

            return;
        }

        if ($event instanceof AfterObjectEventInterface) {
            $propertyPath = $this->path->copyWithProperty($this->propertyName);
            yield new BeforePropertyEvent($this->propertyName, $propertyPath);
            yield from $valueWalker->createEventIterator($this->value, $propertyPath);
            yield new AfterPropertyEvent($this->propertyName, $propertyPath);
        }

        yield $event;
    }
}
