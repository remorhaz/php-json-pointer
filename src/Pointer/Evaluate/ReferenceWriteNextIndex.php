<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceWriteNextIndex extends ReferenceWrite
{


    protected function performNonExisting()
    {
        if (!$this->canPerformNonExisting()) {
            $this
                ->getAdvancer()
                ->fail();
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
