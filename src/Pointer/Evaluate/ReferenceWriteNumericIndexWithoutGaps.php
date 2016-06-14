<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceWriteNumericIndexWithoutGaps extends ReferenceWriteNumericIndex
{

    protected function canPerformNonExisting()
    {
        $isLastReference = $this
            ->getReference()
            ->isLast();
        if ($isLastReference) {
            return
                0 == $this->getIndex() && empty($this->dataCursor) ||
                array_key_exists($this->getIndex() - 1, $this->dataCursor);
        }
        return false;
    }
}
