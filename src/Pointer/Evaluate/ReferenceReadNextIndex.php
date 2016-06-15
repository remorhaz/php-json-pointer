<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceReadNextIndex extends ReferenceEvaluate
{


    public function perform()
    {
        throw new EvaluateException("Next index in array is write-only");
    }
}
