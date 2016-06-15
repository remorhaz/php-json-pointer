<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceWriteNextIndex extends ReferenceWrite
{


    protected function createAdvancer()
    {
        return AdvancerNewIndex::factory();
    }


    protected function performNonExisting()
    {
        if (!$this->canPerformNonExisting()) {
            throw new EvaluateException("Cannot write to non-existing next index of array if it is not last");
        }
        $this
            ->getAdvancer()
            ->write($this->getValue());
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
