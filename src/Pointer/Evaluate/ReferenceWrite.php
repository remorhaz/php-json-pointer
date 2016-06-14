<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

abstract class ReferenceWrite extends ReferenceEvaluate
{
    
    protected $value;

    protected $isValueSet = false;


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
}
