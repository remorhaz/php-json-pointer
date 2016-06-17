<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceTestFactory extends ReferenceEvaluateFactory
{


    protected function createProperty()
    {
        return ReferenceTest::factory()
            ->setAdvancer(AdvancerProperty::factory());
    }


    protected function createNextIndex()
    {
        return ReferenceTest::factory()
            ->setAdvancer(AdvancerNextIndex::factory());
    }


    protected function createNumericIndex()
    {
        return ReferenceTest::factory()
            ->setAdvancer(AdvancerNumericIndex::factory());
    }


    protected function createAllowedNonNumericIndex()
    {
        $advancer = AdvancerNonNumericIndex::factory()
            ->allow();
        return ReferenceTest::factory()
            ->setAdvancer($advancer);
    }


    protected function createNotAllowedNonNumericIndex()
    {
        return ReferenceTest::factory()
            ->setAdvancer(AdvancerNonNumericIndex::factory());
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
