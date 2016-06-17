<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceTestFactory extends ReferenceEvaluateFactory
{


    protected function createProperty()
    {
        return ReferenceTestProperty::factory()
            ->setAdvancer(AdvancerProperty::factory());
    }


    protected function createNextIndex()
    {
        return ReferenceTestNextIndex::factory();
    }


    protected function createNumericIndex()
    {
        return ReferenceTestNumericIndex::factory()
            ->setAdvancer(AdvancerNumericIndex::factory());
    }


    protected function createAllowedNonNumericIndex()
    {
        return ReferenceTestAllowedNonNumericIndex::factory()
            ->setAdvancer(AdvancerNonNumericIndex::factory());
    }


    protected function createNotAllowedNonNumericIndex()
    {
        return ReferenceTestNotAllowedNonNumericIndex::factory();
    }


    protected function createUnknownIndex()
    {
        $reference = $this->getReference();
        throw new DomainException(
            "Failed to create array test evaluator for reference of type {$reference->getType()}"
        );
    }


    protected function createReferenceScalar()
    {
        $reference = $this->getReference();
        throw new EvaluateException("Cannot test non-structured data by reference '{$reference->getValue()}'");
    }
}
