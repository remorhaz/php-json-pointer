<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

/**
 * @package Remorhaz\JSONPointer\Pointer\Evaluate
 */
class LocatorWrite extends LocatorEvaluate
{

    protected $value;

    protected $isValueSet = false;

    protected $numericIndexGaps = false;


    public function setValue($value)
    {
        $this->value = $value;
        $this->isValueSet = true;
        return $this;
    }


    protected function getValue()
    {
        if (!$this->isValueSet) {
            throw new LogicException("Value is not set in evaluator");
        }
        return $this->value;
    }


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


    protected function processCursor()
    {
        $this->dataCursor = $this->getValue();
        $result = null;
        return $this->setResult($result);
    }


    protected function setupReferenceEvaluate(Reference $reference)
    {
        $referenceEvaluate = parent::setupReferenceEvaluate($reference)
            ->getReferenceEvaluate();
        if ($referenceEvaluate instanceof ReferenceWrite) {
            $referenceEvaluate->setValue($this->getValue());
        }
        return $this;
    }


    protected function createReferenceEvaluateProperty(Reference $reference)
    {
        return ReferenceWriteProperty::factory()
            ->setReference($reference);
    }


    protected function createReferenceEvaluateNextIndex(Reference $reference)
    {
        return ReferenceWriteNextIndex::factory()
            ->setReference($reference);
    }


    protected function createReferenceEvaluateNumericIndex(Reference $reference)
    {
        return $this->numericIndexGaps
            ? ReferenceWriteNumericIndexWithGaps::factory()
                ->setReference($reference)
            : ReferenceWriteNumericIndexWithoutGaps::factory()
                ->setReference($reference);
    }


    protected function createReferenceEvaluateAllowedNonNumericIndex(Reference $reference)
    {
        return ReferenceWriteAllowedNonNumericIndex::factory()
            ->setReference($reference);
    }


    protected function createReferenceEvaluateNotAllowedNonNumericIndex(Reference $reference)
    {
        return ReferenceWriteNotAllowedNonNumericIndex::factory()
            ->setReference($reference);
    }


    protected function createReferenceEvaluateUnknownIndex(Reference $reference)
    {
        throw new DomainException(
            "Failed to create array write evaluator for reference of type {$reference->getType()}"
        );
    }


    protected function createReferenceEvaluateScalar(Reference $reference)
    {
        throw new EvaluateException("Cannot write non-structured data by reference '{$reference->getText()}'");
    }
}
