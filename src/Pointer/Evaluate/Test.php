<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

class Test extends \Remorhaz\JSONPointer\Pointer\Evaluate
{


    protected function processCursor()
    {
        $result = true;
        return $this->setResult($result);
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


    protected function processNotAllowedNonNumericArrayIndex(Reference $reference)
    {
        throw new LogicException("Deprecated method");
    }


    /**
     * @param Reference $reference
     * @return ReferenceEvaluate
     */
    protected function createReferenceEvaluate(Reference $reference)
    {
        if (is_object($this->cursor)) {
            return ReferenceTestProperty::factory();
        }
        if (is_array($this->cursor)) {
            switch ($reference->getType()) {
                case $reference::TYPE_NEXT_INDEX:
                    return ReferenceTestNextIndex::factory();

                case $reference::TYPE_INDEX:
                    return ReferenceTestNumericIndex::factory();

                case $reference::TYPE_PROPERTY:
                    return $this->nonNumericIndices
                        ? ReferenceTestAllowedNonNumericIndex::factory()
                        : ReferenceTestNotAllowedNonNumericIndex::factory();

                default:
                    throw new DomainException(
                        "Failed to create test evaluator for reference of type {$reference->getType()}"
                    );
            }
        }
        throw new EvaluateException("Cannot test non-structured data by reference");
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
