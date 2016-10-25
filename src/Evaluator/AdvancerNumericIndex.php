<?php

namespace Remorhaz\JSONPointer\Evaluator;

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


    public function getKeyDescription(): string
    {
        return "{$this->getKey()}";
    }


    public function canWrite(): bool
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
