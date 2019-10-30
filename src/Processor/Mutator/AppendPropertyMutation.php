<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Mutator;

use Generator;
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

    private $value;

    private $path;

    private $propertyName;

    public function __construct(NodeValueInterface $value, PathInterface $path, string $propertyName)
    {
        $this->value = $value;
        $this->path = $path;
        $this->propertyName = $propertyName;
    }

    public function __invoke(EventInterface $event, ValueWalkerInterface $valueWalker)
    {
        return $this->createEventGenerator($event, $valueWalker);
    }

    public function reset(): void
    {
    }

    private function createEventGenerator(EventInterface $event, ValueWalkerInterface $valueWalker): Generator
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
