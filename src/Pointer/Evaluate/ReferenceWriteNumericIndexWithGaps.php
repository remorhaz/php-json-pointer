<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceWriteNumericIndexWithGaps extends ReferenceWriteNumericIndex
{
    
    
    protected function canPerformNonExisting()
    {
        return $this
            ->getReference()
            ->isLast();
    }
}
