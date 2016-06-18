<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

abstract class AdvancerIndex extends Advancer
{


    public function canAdvance()
    {
        return array_key_exists($this->getKey(), $this->getCursor()->getData());
    }


    public function advance()
    {
        $cursorData = &$this
            ->getCursor()
            ->getData();
        $cursorData = &$cursorData[$this->getKey()];
        $this
            ->getCursor()
            ->setData($cursorData);
        return $this;
    }


    public function write($data)
    {
        $cursorData = &$this
            ->getCursor()
            ->getData();
        $cursorData[$this->getKey()] = $data;
        return $this;
    }


    public function fail()
    {
        throw new EvaluateException("Array index {$this->getKeyDescription()} is not found");
    }
}