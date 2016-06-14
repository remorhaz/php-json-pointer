<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

class Write extends \Remorhaz\JSONPointer\Pointer\Evaluate
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
        $this->cursor = $this->getValue();
        $result = null;
        return $this->setResult($result);
    }


    protected function processNextArrayIndex(Reference $reference)
    {
        throw new LogicException("Deprecated method");
    }


    protected function processNonExistingObjectProperty(Reference $reference)
    {
        throw new LogicException("Deprecated method");
    }


    protected function processNonExistingArrayIndex(Reference $reference)
    {
        throw new LogicException("Deprecated method");
    }


    /**
     * @param Reference $reference
     * @return ReferenceEvaluate
     * @throws EvaluateException
     */
    protected function createReferenceEvaluate(Reference $reference)
    {
        if (is_object($this->cursor)) {
            return ReferenceWriteProperty::factory()
                ->setValue($this->getValue());
        } elseif (is_array($this->cursor)) {
            switch ($reference->getType()) {
                case $reference::TYPE_NEXT_INDEX:
                    return ReferenceWriteNextIndex::factory()
                        ->setValue($this->getValue());

                case $reference::TYPE_INDEX:
                    $referenceEvaluate = $this->numericIndexGaps
                        ? ReferenceWriteNumericIndexWithGaps::factory()
                        : ReferenceWriteNumericIndexWithoutGaps::factory();
                    return $referenceEvaluate->setValue($this->getValue());

                case $reference::TYPE_PROPERTY:
                    if (!$this->nonNumericIndices) {
                        throw new EvaluateException(
                            "Accessing non-numeric index '{$reference->getValue()}' in array"
                        );
                    }
                    return ReferenceWriteNonNumericIndex::factory()
                        ->setValue($this->getValue());

                default:
                    throw new DomainException(
                        "Failed to create evaluator for reference of type {$reference->getType()}"
                    );
            }
        }
        throw new EvaluateException("Cannot write into non-structured data by reference");
    }


    protected function processReferenceEvaluate(Reference $reference)
    {
        $referenceEvaluate = $this
            ->createReferenceEvaluate($reference)
            ->setReference($reference)
            ->setData($this->cursor)
            ->perform();
        $this->cursor = &$referenceEvaluate->getData();
        if ($referenceEvaluate->isResultSet()) {
            $this->setResult($referenceEvaluate->getResult());
        }
        return $this;
    }
}
