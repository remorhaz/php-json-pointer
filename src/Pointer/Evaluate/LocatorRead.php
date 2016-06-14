<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

class LocatorRead extends LocatorEvaluate
{


    protected function processCursor()
    {
        return $this->setResult($this->cursor);
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
}
