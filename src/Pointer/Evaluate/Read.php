<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

class Read extends \Remorhaz\JSONPointer\Pointer\Evaluate
{


    protected function processCursor()
    {
        return $this->setResult($this->cursor);
    }


    protected function processNextArrayIndex(Reference $reference)
    {
        throw new EvaluateException("Index '{$reference->getValue()}' in array is write-only");
    }


    protected function processNonExistingObjectProperty(Reference $reference)
    {
        throw new EvaluateException("No property '{$reference->getValue()}' in object");
    }


    protected function processNonExistingArrayIndex(Reference $reference)
    {
        throw new EvaluateException("No index '{$reference->getValue()}' in array");
    }


    protected function createReferenceEvaluate(Reference $reference)
    {

    }
}
