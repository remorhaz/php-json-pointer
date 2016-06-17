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


    protected function createAdvancerForReference()
    {
        $advancer = parent::createAdvancerForReference();
        if ($advancer instanceof AdvancerNumericIndex && $this->numericIndexGaps) {
            $advancer->allowGaps();
        }
        return $advancer;
    }


    protected function processLocator()
    {
        $data = &$this->dataCursor->getData();
        $data = $this->getValue();
        $result = null;
        return $this->setResult($result);
    }


    protected function createReferenceEvaluate()
    {
        return ReferenceWrite::factory()
            ->setValue($this->getValue());
    }
}
