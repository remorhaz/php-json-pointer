<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

class Read extends \Remorhaz\JSONPointer\Pointer\Evaluate
{


    protected function processCursor()
    {
        return $this->setResult($this->cursor);
    }


    protected function processNextArrayIndex(Reference $reference)
    {
        throw new LogicException("Deprecated method");
    }


    protected function processNonExistingObjectProperty(Reference $reference)
    {
        throw new LogicException("Deprecated method");
    }


    protected function processNonExistingArrayIndex(Reference $reference)
    {
        throw new LogicException("Deprecated method");
    }


    protected function createReferenceEvaluate(Reference $reference)
    {
        if (is_object($this->cursor)) {
            return ReferenceReadProperty::factory();
        }
        if (is_array($this->cursor)) {
            switch ($reference->getType()) {
                case $reference::TYPE_NEXT_INDEX:
                    return ReferenceReadNextIndex::factory();

                case $reference::TYPE_INDEX:
                    return ReferenceReadNumericIndex::factory();

                case $reference::TYPE_PROPERTY:
                    return $this->nonNumericIndices
                        ? ReferenceReadAllowedNonNumericIndex::factory()
                        : ReferenceReadNotAllowedNonNumericIndex::factory();

                default:
                    throw new DomainException(
                        "Failed to create read evaluator for reference of type {$reference->getType()}"
                    );
            }
        }
        throw new EvaluateException("Cannot read non-structured data by reference");
    }


    protected function processReferenceEvaluate(Reference $reference)
    {
        $this
            ->setupReferenceEvaluate($reference)
            ->getReferenceEvaluate()
            ->perform();
        $this->cursor = &$this
            ->getReferenceEvaluate()
            ->getData();
        $isReferenceResultSet = $this
            ->getReferenceEvaluate()
            ->isResultSet();
        if ($isReferenceResultSet) {
            $referenceResult = &$this
                ->getReferenceEvaluate()
                ->getResult();
            $this->setResult($referenceResult);
        }
        return $this;
    }
}
