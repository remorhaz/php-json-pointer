<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceReadNotAllowedNonNumericIndex extends ReferenceEvaluate
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
        throw new EvaluateException(
            "Cannot read non-numeric index '{$this->getIndex()}' in array"
        );
    }


    protected function getIndex()
    {
        return $this
            ->getReference()
            ->getValue();
    }
}
