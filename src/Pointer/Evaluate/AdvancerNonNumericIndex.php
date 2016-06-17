<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class AdvancerNonNumericIndex extends AdvancerIndex
{


    protected $isAllowed = false;


    public function allow()
    {
        $this->isAllowed = true;
        return $this;
    }


    public function forbid()
    {
        $this->isAllowed = false;
        return $this;
    }


    public function canAdvance()
    {
        return $this->isAllowed && parent::canAdvance();
    }


    public function fail()
    {
        if ($this->isAllowed) {
            return parent::fail();
        }
        throw new EvaluateException(
            "Non-numeric array index {$this->getValueDescription()} is not allowed"
        );
    }
}
