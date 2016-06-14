<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceReadNextIndex extends ReferenceEvaluate
{


    protected function doesExist()
    {
        return false;
    }


    protected function performExisting()
    {
        throw new LogicException("Next index never exists");
    }


    protected function performNonExisting()
    {
        throw new EvaluateException("Next index in array is write-only");
    }
}
