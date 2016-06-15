<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceWriteNotAllowedNonNumericIndex extends ReferenceEvaluate
{


    public function perform()
    {
        $indexDescription = $this
            ->getReference()
            ->getValue();
        throw new EvaluateException(
            "Cannot write to non-numeric index '{$indexDescription}' in array"
        );
    }
}
