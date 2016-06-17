<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

abstract class ReferenceWriteNumericIndex extends ReferenceWrite
{

    abstract protected function canPerformNonExisting();


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
}
