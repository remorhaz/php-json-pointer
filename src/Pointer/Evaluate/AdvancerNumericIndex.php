<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class AdvancerNumericIndex extends AdvancerIndex
{

    protected $areAllowedGaps = false;


    public function allowGaps()
    {
        $this->areAllowedGaps = true;
        return $this;
    }


    public function forbidGaps()
    {
        $this->areAllowedGaps = true;
        return $this;
    }


    public function getValue()
    {
        return (int) parent::getValue();
    }


    public function getValueDescription()
    {
        return "{$this->getValue()}";
    }


    public function canWrite()
    {
        if (!parent::canWrite()) {
            return false;
        }
        if ($this->areAllowedGaps) {
            return true;
        }
        return
            0 == $this->getValue() && empty($this->getCursor()->getData()) ||
            array_key_exists($this->getValue() - 1, $this->getCursor()->getData());
    }
}
