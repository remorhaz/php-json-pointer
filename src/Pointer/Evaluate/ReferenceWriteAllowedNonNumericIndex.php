<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceWriteAllowedNonNumericIndex extends ReferenceWrite
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
        if (!$this->canPerformNonExisting()) {
            throw new EvaluateException(
                "Cannot write to non-existing index '{$this->getIndex()}'' in array"
            );
        }
        $this->data[$this->getIndex()] = $this->getValue();
        $result = null;
        return $this->setResult($result);
    }


    protected function canPerformNonExisting()
    {
        return $this
            ->getReference()
            ->isLast();
    }


    protected function getIndex()
    {
        return $this
            ->getReference()
            ->getValue();
    }
}
