<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

class LocatorRead extends LocatorEvaluate
{


    protected function processLocator()
    {
        return $this->setResult($this->dataCursor->getData());
    }

    
    protected function createReferenceEvaluate()
    {
        return ReferenceRead::factory();
    }
}
