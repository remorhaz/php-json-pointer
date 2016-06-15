<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceWriteAllowedNonNumericIndex extends ReferenceWrite
{


    protected function createAdvancer()
    {
        return AdvancerNonNumericIndex::factory();
    }


    protected function performNonExisting()
    {
        if (!$this->canPerformNonExisting()) {
            $indexDescription = $this
                ->getAdvancer()
                ->getValueDescription();
            throw new EvaluateException(
                "Cannot write to non-existing index {$indexDescription} in array"
            );
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
