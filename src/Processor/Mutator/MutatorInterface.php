<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Mutator;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;

interface MutatorInterface
{

    public function mutate(NodeValueInterface $rootNode, MutationInterface $mutation): ?NodeValueInterface;
}
