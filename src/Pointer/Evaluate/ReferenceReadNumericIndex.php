<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceReadNumericIndex extends ReferenceEvaluate
{


    protected function doesExist()
    {
        return array_key_exists($this->getIndex(), $this->getData());
    }


    protected function performExisting()
    {
        $this->data = &$this->data[$this->getIndex()];
        return $this;
    }


    protected function performNonExisting()
    {
        throw new EvaluateException("No index {$this->getIndex()} in array");
    }


    protected function getIndex()
    {
        return (int) $this
            ->getReference()
            ->getValue();
    }
}
