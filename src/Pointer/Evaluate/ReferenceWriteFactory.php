<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceWriteFactory extends ReferenceEvaluateFactory
{

    /**
     * @var bool
     */
    protected $numericIndexGaps = false;


    public function allowNumericIndexGaps()
    {
        $this->numericIndexGaps = true;
        return $this;
    }


    public function forbidNumericIndexGaps()
    {
        $this->numericIndexGaps = false;
        return $this;
    }


    protected function createProperty()
    {
        return ReferenceWrite::factory()
            ->setAdvancer(AdvancerProperty::factory());
    }


    protected function createNextIndex()
    {
        return ReferenceWrite::factory()
            ->setAdvancer(AdvancerNextIndex::factory());
    }


    protected function createNumericIndex()
    {
        $advancer = AdvancerNumericIndex::factory();
        if ($this->numericIndexGaps) {
            $advancer->allowGaps();
        }
        return ReferenceWrite::factory()
            ->setAdvancer($advancer);
    }


    protected function createAllowedNonNumericIndex()
    {
        $advancer = AdvancerNonNumericIndex::factory()
            ->allow();
        return ReferenceWrite::factory()
            ->setAdvancer($advancer);
    }


    protected function createNotAllowedNonNumericIndex()
    {
        return ReferenceWrite::factory()
            ->setAdvancer(AdvancerNonNumericIndex::factory());
    }


    protected function createUnknownIndex()
    {
        $reference = $this->getReference();
        throw new DomainException(
            "Failed to create array write evaluator for reference of type {$reference->getType()}"
        );
    }


    protected function createReferenceScalar()
    {
        $reference = $this->getReference();
        throw new EvaluateException(
            "Cannot write non-structured data by reference '{$reference->getValue()}'"
        );
    }
}
