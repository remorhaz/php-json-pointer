<?php

namespace Remorhaz\JSONPointer\Evaluator;

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


    public function canAdvance(): bool
    {
        return $this->isAllowed && parent::canAdvance();
    }


    public function canWrite(): bool
    {
        return $this->isAllowed && parent::canWrite();
    }


    public function fail()
    {
        if ($this->isAllowed) {
            return parent::fail();
        }
        throw new EvaluatorException(
            "Non-numeric array index {$this->getKeyDescription()} is not allowed"
        );
    }
}
