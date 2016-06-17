<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceReadFactory extends ReferenceEvaluateFactory
{


    protected function createProperty()
    {
        return ReferenceRead::factory()
            ->setAdvancer(AdvancerProperty::factory());
    }


    protected function createNextIndex()
    {
        return ReferenceRead::factory()
            ->setAdvancer(AdvancerNextIndex::factory());
    }


    protected function createNumericIndex()
    {
        return ReferenceRead::factory()
            ->setAdvancer(AdvancerNumericIndex::factory());
    }


    protected function createAllowedNonNumericIndex()
    {
        $advancer = AdvancerNonNumericIndex::factory()
            ->allow();
        return ReferenceRead::factory()
            ->setAdvancer($advancer);
    }


    protected function createNotAllowedNonNumericIndex()
    {
        return ReferenceRead::factory()
            ->setAdvancer(AdvancerNonNumericIndex::factory());
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
