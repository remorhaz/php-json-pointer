<?php

namespace Remorhaz\JSONPointer\Evaluator;

/**
 * @package Remorhaz\JSONPointer\Pointer\Evaluator
 */
class LocatorEvaluatorWrite extends LocatorEvaluator
{

    private $value;

    private $isValueSet = false;

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


    protected function createAdvancer()
    {
        $advancer = parent::createAdvancer();
        if ($advancer instanceof AdvancerNumericIndex && $this->numericIndexGaps) {
            $advancer->allowGaps();
        }
        return $advancer;
    }


    protected function evaluateLocator()
    {
        $data = &$this
            ->getCursor()
            ->getData();
        $data = $this->getValue();
        $result = null;
        return $this->setResult($result);
    }


    protected function createReferenceEvaluator()
    {
        return ReferenceEvaluatorWrite::factory()
            ->setAdvancer($this->createAdvancer())
            ->setValue($this->getValue());
    }
}
