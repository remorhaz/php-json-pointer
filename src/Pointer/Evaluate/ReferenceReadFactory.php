<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceReadFactory extends ReferenceEvaluateFactory
{


    protected function createProperty()
    {
        return ReferenceReadProperty::factory()
            ->setAdvancer(AdvancerProperty::factory());
    }


    protected function createNextIndex()
    {
        return ReferenceReadNextIndex::factory();
    }


    protected function createNumericIndex()
    {
        return ReferenceReadNumericIndex::factory()
            ->setAdvancer(AdvancerNumericIndex::factory());
    }


    protected function createAllowedNonNumericIndex()
    {
        return ReferenceReadAllowedNonNumericIndex::factory()
            ->setAdvancer(AdvancerNonNumericIndex::factory());
    }


    protected function createNotAllowedNonNumericIndex()
    {
        return ReferenceReadNotAllowedNonNumericIndex::factory();
    }


    protected function createUnknownIndex()
    {
        $reference = $this->getReference();
        throw new DomainException(
            "Failed to create array read evaluator for reference of type {$reference->getType()}"
        );
    }


    protected function createReferenceScalar()
    {
        $reference = $this->getReference();
        throw new EvaluateException(
            "Cannot read non-structured data by reference '{$reference->getText()}'"
        );
    }
}
