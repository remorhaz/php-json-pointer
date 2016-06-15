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
        $factory = $this
            ->getReferenceEvaluateFactory();
        if ($factory instanceof ReferenceWriteFactory) {
            $factory->allowNumericIndexGaps();
        }
        return $this;
    }


    public function forbidNumericIndexGaps()
    {
        $factory = $this
            ->getReferenceEvaluateFactory();
        if ($factory instanceof ReferenceWriteFactory) {
            $factory->forbidNumericIndexGaps();
        }
        return $this;
    }


    protected function processCursor()
    {
        $this->dataCursor = $this->getValue();
        $result = null;
        return $this->setResult($result);
    }


    protected function createReferenceEvaluateFactory()
    {
        return ReferenceWriteFactory::factory();
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
}
