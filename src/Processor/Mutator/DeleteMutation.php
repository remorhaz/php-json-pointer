<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Mutator;

use Generator;
use Iterator;
use Remorhaz\JSON\Data\Event\EventInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;

final class DeleteMutation implements MutationInterface
{

    private $path;

    public function __construct(PathInterface $path)
    {
        $this->path = $path;
    }

    public function __invoke(EventInterface $event, ValueWalkerInterface $valueWalker): Iterator
    {
        return $this->createEventGenerator($event);
    }

    public function reset(): void
    {
    }

    private function createEventGenerator(EventInterface $event): Generator
    {
        if ($this->path->contains($event->getPath())) {
            return;
        }
        yield $event;
    }
}
