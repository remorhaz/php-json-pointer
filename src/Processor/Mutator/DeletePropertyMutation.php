<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Mutator;

use Iterator;
use Remorhaz\JSON\Data\Event\EventInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;

final class DeletePropertyMutation implements MutationInterface
{
    public function __construct(
        private readonly PathInterface $path,
    ) {
    }

    public function __invoke(EventInterface $event, ValueWalkerInterface $valueWalker): Iterator
    {
        if ($this->path->contains($event->getPath())) {
            return;
        }
        yield $event;
    }

    public function reset(): void
    {
    }
}
