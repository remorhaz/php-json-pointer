<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceReadProperty extends ReferenceEvaluate
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
        throw new EvaluateException("No property '{$this->getProperty()}' in object");
    }


    protected function getProperty()
    {
        return $this
            ->getReference()
            ->getValue();
    }
}
