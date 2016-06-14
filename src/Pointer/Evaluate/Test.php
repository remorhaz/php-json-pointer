<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

class Test extends \Remorhaz\JSONPointer\Pointer\Evaluate
{


    protected function processCursor()
    {
        $result = true;
        return $this->setResult($result);
    }


    protected function processNextArrayIndex(Reference $reference)
    {
        $result = false;
        return $this->setResult($result);
    }


    protected function processNonExistingObjectProperty(Reference $reference)
    {
        $result = false;
        return $this->setResult($result);
    }


    protected function processNonExistingArrayIndex(Reference $reference)
    {
        $result = false;
        return $this->setResult($result);
    }


    protected function processNotAllowedNonNumericArrayIndex(Reference $reference)
    {
        $result = false;
        return $this->setResult($result);
    }
    
    
    protected function createReferenceEvaluate(Reference $reference)
    {
        
    }
}
