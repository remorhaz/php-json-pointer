<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceTestProperty extends ReferenceEvaluate
{


    protected function doesExist()
    {
        return property_exists($this->getDataCursor(), $this->getProperty());
    }


    protected function performExisting()
    {
        $this->dataCursor = &$this->dataCursor->{$this->getProperty()};
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
