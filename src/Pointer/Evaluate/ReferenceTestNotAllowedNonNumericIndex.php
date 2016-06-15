<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceTestNotAllowedNonNumericIndex extends ReferenceEvaluate
{


    public function perform()
    {
        $result = false;
        return $this->setResult($result);
    }
}
