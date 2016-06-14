<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

class LocatorRead extends LocatorEvaluate
{


    protected function processCursor()
    {
        return $this->setResult($this->cursor);
    }


    protected function createReferenceEvaluateProperty(Reference $reference)
    {
        return ReferenceReadProperty::factory()
            ->setReference($reference);
    }


    protected function createReferenceEvaluateNextIndex(Reference $reference)
    {
        return ReferenceReadNextIndex::factory()
            ->setReference($reference);
    }


    protected function createReferenceEvaluateNumericIndex(Reference $reference)
    {
        return ReferenceReadNumericIndex::factory()
            ->setReference($reference);
    }


    protected function createReferenceEvaluateAllowedNonNumericIndex(Reference $reference)
    {
        return ReferenceReadAllowedNonNumericIndex::factory()
            ->setReference($reference);
    }


    protected function createReferenceEvaluateNotAllowedNonNumericIndex(Reference $reference)
    {
        return ReferenceReadNotAllowedNonNumericIndex::factory()
            ->setReference($reference);
    }


    protected function createReferenceEvaluateUnknownIndex(Reference $reference)
    {
        throw new DomainException(
            "Failed to create array read evaluator for reference of type {$reference->getType()}"
        );
    }


    protected function createReferenceEvaluateScalar(Reference $reference)
    {
        throw new EvaluateException("Cannot read non-structured data by reference");
    }
}
