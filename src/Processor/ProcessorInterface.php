<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Processor\Result\ResultInterface;
use Remorhaz\JSON\Pointer\Query\QueryInterface;

interface ProcessorInterface
{

    public function select(QueryInterface $query, NodeValueInterface $rootNode): ResultInterface;

    public function delete(QueryInterface $query, NodeValueInterface $rootNode): ResultInterface;

    public function replace(
        QueryInterface $query,
        NodeValueInterface $rootNode,
        NodeValueInterface $value
    ): ResultInterface;

    public function add(
        QueryInterface $query,
        NodeValueInterface $rootNode,
        NodeValueInterface $value
    ): ResultInterface;
}
