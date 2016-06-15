<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class AdvancerNumericIndex extends AdvancerNonNumericIndex
{


    public function getValue()
    {
        return (int) parent::getValue();
    }


    public function getValueDescription()
    {
        return "{$this->getValue()}";
    }
}
