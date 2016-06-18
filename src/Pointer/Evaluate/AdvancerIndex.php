<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

abstract class AdvancerIndex extends Advancer
{


    public function canAdvance()
    {
        return array_key_exists($this->getValue(), $this->getCursor()->getData());
    }


    public function advance()
    {
        $data = &$this
            ->getCursor()
            ->getData()[$this->getValue()];
        $this
            ->getCursor()
            ->setData($data);
        return $this;
    }


    public function write($data)
    {
        $this->getCursor()->getData()[$this->getValue()] = $data;
        return $this;
    }


    public function fail()
    {
        throw new EvaluateException("Array index {$this->getValueDescription()} is not found");
    }
}
