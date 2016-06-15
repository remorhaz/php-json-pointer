<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

class LocatorRead extends LocatorEvaluate
{


    protected function processCursor()
    {
        return $this->setResult($this->dataCursor);
    }

    
    protected function createReferenceEvaluateFactory()
    {
        return ReferenceReadFactory::factory();
    }
}
