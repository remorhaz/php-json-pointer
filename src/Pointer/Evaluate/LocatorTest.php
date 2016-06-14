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


    protected function createReferenceEvaluateProperty(Reference $reference)
    {
        return ReferenceTestProperty::factory()
            ->setReference($reference);
    }


    protected function createReferenceEvaluateNextIndex(Reference $reference)
    {
        return ReferenceTestNextIndex::factory()
            ->setReference($reference);
    }


    protected function createReferenceEvaluateNumericIndex(Reference $reference)
    {
        return ReferenceTestNumericIndex::factory()
            ->setReference($reference);
    }


    protected function createReferenceEvaluateAllowedNonNumericIndex(Reference $reference)
    {
        return ReferenceTestAllowedNonNumericIndex::factory()
            ->setReference($reference);
    }


    protected function createReferenceEvaluateNotAllowedNonNumericIndex(Reference $reference)
    {
        return ReferenceTestNotAllowedNonNumericIndex::factory()
            ->setReference($reference);
    }


    protected function createReferenceEvaluateUnknownIndex(Reference $reference)
    {
        throw new DomainException(
            "Failed to create array test evaluator for reference of type {$reference->getType()}"
        );
    }


    protected function createReferenceEvaluateScalar(Reference $reference)
    {
        throw new EvaluateException("Cannot test non-structured data by reference '{$reference->getText()}'");
    }
}
