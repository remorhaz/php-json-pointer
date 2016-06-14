<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceWriteProperty extends ReferenceWrite
{


    protected function doesExist()
    {
        return property_exists($this->getData(), $this->getProperty());
    }


    protected function performExisting()
    {
        $this->data = &$this->data->{$this->getProperty()};
        return $this;
    }


    protected function performNonExisting()
    {
        if (!$this->canPerformNonExisting()) {
            throw new EvaluateException("Cannot write to non-existing property if it is not last");
        }
        $this->data->{$this->getProperty()} = $this->getValue();
        $result = null;
        return $this->setResult($result);
    }


    protected function getProperty()
    {
        return $this
            ->getReference()
            ->getValue();
    }
    
    
    protected function canPerformNonExisting()
    {
        return $this
            ->getReference()
            ->isLast();
    }
}
