<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

abstract class ReferenceWriteNumericIndex extends ReferenceWrite
{

    abstract protected function canPerformNonExisting();


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
        $index = $this->getIndex();
        if (!$this->canPerformNonExisting()) {
            $indexText = is_int($index) ? "{$index}" : "'{$index}'";
            throw new EvaluateException("Cannot write to non-existing index {$indexText} in array");
        }
        $this->data[$index] = $this->getValue();
        $result = null;
        return $this->setResult($result);
    }


    protected function getIndex()
    {
        return (int) $this
            ->getReference()
            ->getValue();
    }
}
