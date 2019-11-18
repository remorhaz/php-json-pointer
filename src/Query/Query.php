<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ObjectValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Pointer\Locator\IndexReferenceInterface;
use Remorhaz\JSON\Pointer\Locator\LocatorInterface;
use Remorhaz\JSON\Pointer\Locator\NextIndexReferenceInterface;

final class Query implements QueryInterface
{

    private $source;

    private $locator;

    public function __construct(string $source, LocatorInterface $locator)
    {
        $this->source = $source;
        $this->locator = $locator;
    }

    public function __invoke(NodeValueInterface $rootNode): QueryResultInterface
    {
        $node = $rootNode;
        $parentNode = null;
        $lastReference = null;
        foreach ($this->locator->references() as $listedReference) {
            if ($listedReference->isLast()) {
                $lastReference = $listedReference->getReference();
            }
            if ($node instanceof ScalarValueInterface) {
                return new QueryResult($this->source);
            }

            if ($node instanceof ArrayValueInterface) {
                $reference = $listedReference->getReference();
                switch (true) {
                    case $reference instanceof IndexReferenceInterface:
                        $key = $reference->getElementIndex();
                        break;

                    case $reference instanceof NextIndexReferenceInterface:
                        $key = null;
                        break;

                    default:
                        return new QueryResult($this->source);
                }
                $lastIndex = null;
                foreach ($node->createChildIterator() as $index => $element) {
                    $lastIndex = $index;
                    if ($index === $key) {
                        $parentNode = $node;
                        $node = $element;
                        continue 2;
                    }
                }

                if (isset($key, $lastIndex) && $key != $lastIndex + 1) {
                    return new QueryResult($this->source);
                }

                return new QueryResult(
                    $this->source,
                    null,
                    $listedReference->isLast() ? $node : null,
                    $lastReference,
                );
            }

            if ($node instanceof ObjectValueInterface) {
                $key = $listedReference->getReference()->getPropertyName();
                foreach ($node->createChildIterator() as $name => $property) {
                    if ($name == $key) {
                        $parentNode = $node;
                        $node = $property;
                        continue 2;
                    }
                }

                return new QueryResult(
                    $this->source,
                    null,
                    $listedReference->isLast() ? $node : null,
                    $lastReference,
                );
            }
        }

        return new QueryResult($this->source, $node, $parentNode, $lastReference);
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
