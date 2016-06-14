<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceTestNextIndex extends ReferenceEvaluate
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
        $result = false;
        return $this->setResult($result);
    }
}
