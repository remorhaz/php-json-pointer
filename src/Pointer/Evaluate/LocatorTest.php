<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

class LocatorTest extends LocatorEvaluate
{


    protected function processLocator()
    {
        $result = true;
        return $this->setResult($result);
    }


    protected function createReferenceEvaluate()
    {
        return ReferenceTest::factory();
    }
}
