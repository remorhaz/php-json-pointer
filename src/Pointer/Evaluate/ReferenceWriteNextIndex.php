<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceWriteNextIndex extends ReferenceWrite
{


    protected function doesExist()
    {
        return false;
    }


    protected function performExisting()
    {
        throw new LogicException("Next index never exists");
    }


    protected function performNonExisting()
    {
        if (!$this->canPerformNonExisting()) {
            throw new EvaluateException("Cannot write to non-existing next index of array if it is not last");
        }
        $this->data[] = $this->getValue();
        $result = null;
        return $this->setResult($result);
    }


    protected function canPerformNonExisting()
    {
        return $this
            ->getReference()
            ->isLast();
    }
}
