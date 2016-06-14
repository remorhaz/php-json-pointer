<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

/**
 * @package Remorhaz\JSONPointer\Pointer\Evaluate
 */
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
            return ReferenceWriteProperty::factory();
        }
        if (is_array($this->cursor)) {
            switch ($reference->getType()) {
                case $reference::TYPE_NEXT_INDEX:
                    return ReferenceWriteNextIndex::factory();

                case $reference::TYPE_INDEX:
                    return $this->numericIndexGaps
                        ? ReferenceWriteNumericIndexWithGaps::factory()
                        : ReferenceWriteNumericIndexWithoutGaps::factory();

                case $reference::TYPE_PROPERTY:
                    return $this->nonNumericIndices
                        ? ReferenceWriteAllowedNonNumericIndex::factory()
                        : ReferenceWriteNotAllowedNonNumericIndex::factory();

                default:
                    throw new DomainException(
                        "Failed to create write evaluator for reference of type {$reference->getType()}"
                    );
            }
        }
        throw new EvaluateException("Cannot write into non-structured data by reference");
    }


    protected function setupReferenceEvaluate(Reference $reference)
    {
        /** @var ReferenceWrite $referenceEvaluate */
        $referenceEvaluate = parent::setupReferenceEvaluate($reference)
            ->getReferenceEvaluate();
        if (!($referenceEvaluate instanceof ReferenceWrite)) {
            throw new LogicException("Invalid write reference evaluator");
        }
        $referenceEvaluate->setValue($this->getValue());
        return $this;
    }


    protected function processReferenceEvaluate(Reference $reference)
    {
        $this
            ->setupReferenceEvaluate($reference)
            ->getReferenceEvaluate()
            ->perform();
        $this->cursor = &$this
            ->getReferenceEvaluate()
            ->getData();
        $isReferenceResultSet = $this
            ->getReferenceEvaluate()
            ->isResultSet();
        if ($isReferenceResultSet) {
            $referenceResult = &$this
                ->getReferenceEvaluate()
                ->getResult();
            $this->setResult($referenceResult);
        }
        return $this;
    }
}
