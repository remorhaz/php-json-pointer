<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceReadNotAllowedNonNumericIndex extends ReferenceEvaluate
{


    public function perform()
    {
        $indexDescription = $this
            ->getReference()
            ->getValue();
        throw new EvaluateException(
            "Cannot read non-numeric index '{$indexDescription}' in array"
        );
    }
}
