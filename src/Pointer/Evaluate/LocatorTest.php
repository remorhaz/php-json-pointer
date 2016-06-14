<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

class LocatorTest extends LocatorEvaluate
{


    protected function processCursor()
    {
        $result = true;
        return $this->setResult($result);
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
}
