<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor;

use Remorhaz\JSON\Data\Export\EventDecoder;
use Remorhaz\JSON\Data\Export\ValueDecoder;
use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoder;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ObjectValueInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalker;
use Remorhaz\JSON\Pointer\Locator\IndexReferenceInterface;
use Remorhaz\JSON\Pointer\Locator\NextIndexReferenceInterface;
use Remorhaz\JSON\Pointer\Locator\ReferenceInterface;
use Remorhaz\JSON\Pointer\Processor\Mutator\AppendElementMutation;
use Remorhaz\JSON\Pointer\Processor\Mutator\AppendPropertyMutation;
use Remorhaz\JSON\Pointer\Processor\Mutator\DeleteElementMutation;
use Remorhaz\JSON\Pointer\Processor\Mutator\DeletePropertyMutation;
use Remorhaz\JSON\Pointer\Processor\Mutator\InsertElementMutation;
use Remorhaz\JSON\Pointer\Processor\Mutator\Mutator;
use Remorhaz\JSON\Pointer\Processor\Mutator\MutatorInterface;
use Remorhaz\JSON\Pointer\Processor\Mutator\ReplaceMutation;
use Remorhaz\JSON\Pointer\Processor\Result\ExistingResult;
use Remorhaz\JSON\Pointer\Processor\Result\NonExistingResult;
use Remorhaz\JSON\Pointer\Processor\Result\ResultInterface;
use Remorhaz\JSON\Pointer\Query\QueryInterface;
use Remorhaz\JSON\Pointer\Query\QueryResultInterface;

final class Processor implements ProcessorInterface
{

    private $encoder;

    private $decoder;

    private $mutator;

    public static function create(): ProcessorInterface
    {
        $decoder = new ValueDecoder();

        return new self(
            new ValueEncoder($decoder),
            $decoder,
            new Mutator(
                new ValueWalker(),
                new EventDecoder(),
            ),
        );
    }

    public function __construct(
        ValueEncoderInterface $encoder,
        ValueDecoderInterface $decoder,
        MutatorInterface $mutator
    ) {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->mutator = $mutator;
    }

    public function select(QueryInterface $query, NodeValueInterface $rootNode): ResultInterface
    {
        $queryResult = $query($rootNode);

        return $queryResult->hasSelection()
            ? new ExistingResult($this->encoder, $this->decoder, $queryResult->getSelection())
            : new NonExistingResult($query->getSource());
    }

    public function delete(QueryInterface $query, NodeValueInterface $rootNode): ResultInterface
    {
        $mutation = $this->createDeleteMutation($query($rootNode));

        return isset($mutation)
            ? $this->getMutationResult($query, $rootNode, $mutation)
            : new NonExistingResult($query->getSource());
    }

    private function createDeleteMutation(QueryResultInterface $queryResult): ?MutationInterface
    {
        if (!$queryResult->hasSelection() || !$queryResult->hasParent()) {
            return null;
        }
        $selection = $queryResult->getSelection();
        $parent = $queryResult->getParent();
        if ($parent instanceof ArrayValueInterface) {
            return new DeleteElementMutation($parent->getPath(), $selection->getPath());
        }
        if ($parent instanceof ObjectValueInterface) {
            return new DeletePropertyMutation($selection->getPath());
        }

        return null;
    }

    private function getMutationResult(
        QueryInterface $query,
        NodeValueInterface $rootNode,
        MutationInterface $mutation
    ): ResultInterface {
        $mutatedValue = $this
            ->mutator
            ->mutate($rootNode, $mutation);

        return null === $mutatedValue
            ? new NonExistingResult($query->getSource())
            : new ExistingResult($this->encoder, $this->decoder, $mutatedValue);
    }

    public function replace(
        QueryInterface $query,
        NodeValueInterface $rootNode,
        NodeValueInterface $value
    ): ResultInterface {
        $queryResult = $query($rootNode);

        return $queryResult->hasSelection()
            ? $this->getMutationResult(
                $query,
                $rootNode,
                new ReplaceMutation($value, $queryResult->getSelection()->getPath())
            )
            : new NonExistingResult($query->getSource());
    }

    public function add(QueryInterface $query, NodeValueInterface $rootNode, NodeValueInterface $value): ResultInterface
    {
        $mutation = $this->createAddMutation($query($rootNode), $value);

        return isset($mutation)
            ? $this->getMutationResult($query, $rootNode, $mutation)
            : new NonExistingResult($query->getSource());
    }

    private function createAddMutation(QueryResultInterface $queryResult, NodeValueInterface $value): ?MutationInterface
    {
        if (!$queryResult->hasParent()) {
            return $queryResult->hasSelection()
                ? new ReplaceMutation($value, $queryResult->getSelection()->getPath())
                : null;
        }
        if (!$queryResult->hasLastReference()) {
            return null;
        }
        $parent = $queryResult->getParent();
        $reference = $queryResult->getLastReference();
        if ($parent instanceof ObjectValueInterface) {
            return $queryResult->hasSelection()
                ? new ReplaceMutation($value, $queryResult->getSelection()->getPath())
                : new AppendPropertyMutation($value, $parent->getPath(), $reference->getPropertyName());
        }
        if ($parent instanceof ArrayValueInterface) {
            return $queryResult->hasSelection()
                ? $this->createInsertElementMutation($reference, $parent, $value)
                : $this->createAppendElementMutation($reference, $parent, $value);
        }

        return null;
    }

    private function createInsertElementMutation(
        ReferenceInterface $reference,
        NodeValueInterface $parent,
        NodeValueInterface $value
    ): ?MutationInterface {
        return $reference instanceof IndexReferenceInterface
            ? new InsertElementMutation($value, $parent->getPath(), $reference->getElementIndex())
            : null;
    }

    private function createAppendElementMutation(
        ReferenceInterface $reference,
        NodeValueInterface $parent,
        NodeValueInterface $value
    ): ?MutationInterface {
        switch (true) {
            case $reference instanceof NextIndexReferenceInterface:
                $elementIndex = null;
                break;

            case $reference instanceof IndexReferenceInterface:
                $elementIndex = $reference->getElementIndex();
                break;

            default:
                return null;
        }

        return new AppendElementMutation($value, $parent->getPath(), $elementIndex);
    }
}
