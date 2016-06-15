<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

abstract class ReferenceWriteNumericIndex extends ReferenceWrite
{

    abstract protected function canPerformNonExisting();


    protected function createAdvancer()
    {
        return AdvancerNumericIndex::factory();
    }


    protected function performNonExisting()
    {
        if (!$this->canPerformNonExisting()) {
            $indexDescription = $this
                ->getAdvancer()
                ->getValueDescription();
            throw new EvaluateException("Cannot write to non-existing index {$indexDescription} in array");
        }
        $this
            ->getAdvancer()
            ->write($this->getValue());
        $result = null;
        return $this->setResult($result);
    }
}
