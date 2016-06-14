<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceTestNotAllowedNonNumericIndex extends ReferenceEvaluate
{


    protected function doesExist()
    {
        return false; // We don't allow it to exist :)
    }


    protected function performExisting()
    {
        throw new LogicException(
            "Non-numeric index '{$this->getIndex()}' is not allowed to exist"
        );
    }


    protected function performNonExisting()
    {
        $result = false;
        return $this->setResult($result);
    }


    protected function getIndex()
    {
        return $this
            ->getReference()
            ->getValue();
    }
}
