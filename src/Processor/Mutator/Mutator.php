<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Mutator;

use Remorhaz\JSON\Data\Export\EventDecoderInterface;
use Remorhaz\JSON\Data\Path\Path;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;

final class Mutator implements MutatorInterface
{
    public function __construct(
        private ValueWalkerInterface $valueWalker,
        private EventDecoderInterface $eventDecoder,
    ) {
    }

    public function mutate(NodeValueInterface $rootNode, MutationInterface $mutation): ?NodeValueInterface
    {
        $events = $this
            ->valueWalker
            ->createMutableEventIterator($rootNode, new Path(), $mutation);

        return $this
            ->eventDecoder
            ->exportEvents($events);
    }
}
