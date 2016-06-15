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
        return ReferenceWriteProperty::factory();
    }


    protected function createNextIndex()
    {
        return ReferenceWriteNextIndex::factory();
    }


    protected function createNumericIndex()
    {
        return $this->numericIndexGaps
            ? ReferenceWriteNumericIndexWithGaps::factory()
            : ReferenceWriteNumericIndexWithoutGaps::factory();
    }


    protected function createAllowedNonNumericIndex()
    {
        return ReferenceWriteAllowedNonNumericIndex::factory();
    }


    protected function createNotAllowedNonNumericIndex()
    {
        return ReferenceWriteNotAllowedNonNumericIndex::factory();
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
