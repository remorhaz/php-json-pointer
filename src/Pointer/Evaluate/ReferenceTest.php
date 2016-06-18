<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceTest extends ReferenceEvaluate
{


    protected function performNonExisting()
    {
        $result = false;
        return $this->setResult($result);
    }
}
