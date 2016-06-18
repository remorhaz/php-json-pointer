<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluator;

use Remorhaz\JSONPointer\Locator\Reference;

class LocatorEvaluatorTest extends LocatorEvaluator
{


    protected function processLocator()
    {
        $result = true;
        return $this->setResult($result);
    }


    protected function createReferenceEvaluator()
    {
        return ReferenceEvaluatorTest::factory();
    }
}
