<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceTestProperty extends ReferenceEvaluate
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
        $result = false;
        return $this->setResult($result);
    }


    protected function getProperty()
    {
        return $this
            ->getReference()
            ->getValue();
    }
}
