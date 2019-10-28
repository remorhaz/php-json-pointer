<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Mutator;

use Iterator;
use Remorhaz\JSON\Data\Event\AfterArrayEventInterface;
use Remorhaz\JSON\Data\Event\AfterElementEvent;
use Remorhaz\JSON\Data\Event\BeforeElementEvent;
use Remorhaz\JSON\Data\Event\EventInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;

final class AppendElementMutation implements MutationInterface
{

    private $value;

    private $path;

    private $elementIndex;

    private $elementCounter = 0;

    public function __construct(NodeValueInterface $value, PathInterface $path, ?int $elementIndex = null)
    {
        $this->value = $value;
        $this->path = $path;
        $this->elementIndex = $elementIndex;
    }

    public function __invoke(EventInterface $event, ValueWalkerInterface $valueWalker): Iterator
    {
        if ($this->parentPathMatches($event)) {
            if ($event instanceof AfterElementEvent) {
                $this->elementCounter++;
            }
            yield $event;

            return;
        }

        if (!$event->getPath()->equals($this->path)) {
            yield $event;

            return;
        }

        if ($event instanceof AfterArrayEventInterface) {
            $elementPath = $this
                ->path
                ->copyWithElement($this->elementCounter);
            yield new BeforeElementEvent($this->elementCounter, $elementPath);
            yield from $valueWalker->createEventIterator($this->value, $elementPath);
            yield new AfterElementEvent($this->elementCounter, $elementPath);
        }

        yield $event;
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
}
