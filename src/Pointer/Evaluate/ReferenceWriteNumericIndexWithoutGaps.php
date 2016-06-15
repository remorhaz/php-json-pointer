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
            $index = $this
                ->getAdvancer()
                ->getValue();
            return
                0 == $index && empty($this->dataCursor) ||
                array_key_exists($index - 1, $this->dataCursor);
        }
        return false;
    }
}
