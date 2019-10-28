<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ObjectValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Pointer\Locator\LocatorInterface;

final class Query implements QueryInterface
{

    private $source;

    private $locator;

    private $capabilities;

    public function __construct(string $source, LocatorInterface $locator)
    {
        $this->source = $source;
        $this->locator = $locator;
        $this->capabilities = new QueryCapabilities($locator->pointsNewElement());
    }

    public function __invoke(NodeValueInterface $rootNode): QueryResultInterface
    {
        $node = $rootNode;
        $parentNode = null;
        $lastReference = null;
        foreach ($this->locator->getReferenceList() as $reference) {
            if ($reference->isLast()) {
                $lastReference = $reference;
            }
            if ($node instanceof ScalarValueInterface) {
                return new QueryResult;
            }

            if ($node instanceof ArrayValueInterface) {
                switch ($reference->getType()) {
                    case $reference::TYPE_INDEX:
                        $key = (int) $reference->getKey();
                        break;

                    case $reference::TYPE_NEXT_INDEX:
                        $key = null;
                        break;

                    default:
                        return new QueryResult;
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

                if (isset($key) && $key != $lastIndex + 1) {
                    return new QueryResult;
                }

                return new QueryResult(
                    null,
                    $reference->isLast() ? $node : null,
                    $lastReference,
                );
            }

            if ($node instanceof ObjectValueInterface) {
                switch ($reference->getType()) {
                    case $reference::TYPE_NEXT_INDEX:
                    case $reference::TYPE_INDEX:
                    case $reference::TYPE_PROPERTY:
                        $key = (string) $reference->getKey();
                        break;

                    default:
                        return new QueryResult;
                }
                foreach ($node->createChildIterator() as $name => $property) {
                    if ($name == $key) {
                        $parentNode = $node;
                        $node = $property;
                        continue 2;
                    }
                }

                return new QueryResult(
                    null,
                    $reference->isLast() ? $node : null,
                    $lastReference,
                );
            }

            throw new Exception\UnexpectedNodeValueException($node);
        }

        return new QueryResult($node, $parentNode, $lastReference);
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getCapabilities(): QueryCapabilitiesInterface
    {
        return $this->capabilities;
    }
}
