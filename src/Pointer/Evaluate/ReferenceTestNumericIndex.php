<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceTestNumericIndex extends ReferenceEvaluate
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
        $result = false;
        return $this->setResult($result);
    }


    protected function getIndex()
    {
        return (int) $this
            ->getReference()
            ->getValue();
    }
}