<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluator;

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


    public function getKey()
    {
        return (int) parent::getKey();
    }


    public function getKeyDescription()
    {
        return "{$this->getKey()}";
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
            0 == $this->getKey() && empty($this->getCursor()->getData()) ||
            array_key_exists($this->getKey() - 1, $this->getCursor()->getData());
    }
}
