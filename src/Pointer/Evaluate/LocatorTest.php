<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

class LocatorTest extends LocatorEvaluate
{


    protected function processCursor()
    {
        $result = true;
        return $this->setResult($result);
    }


    protected function createReferenceEvaluateFactory()
    {
        return ReferenceTestFactory::factory();
    }
}
