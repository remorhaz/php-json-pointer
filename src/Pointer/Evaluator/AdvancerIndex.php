<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluator;

abstract class AdvancerIndex extends Advancer
{


    public function canAdvance()
    {
        return array_key_exists($this->getKey(), $this->getCursor()->getData());
    }


    protected function &advance(&$cursorData)
    {
        return $cursorData[$this->getKey()];
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
        throw new EvaluatorException("Array index {$this->getKeyDescription()} is not found");
    }
}
