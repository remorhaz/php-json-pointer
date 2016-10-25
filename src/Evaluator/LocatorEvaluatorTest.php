<?php

namespace Remorhaz\JSONPointer\Evaluator;

class LocatorEvaluatorTest extends LocatorEvaluator
{


    protected function evaluateLocator()
    {
        $result = true;
        return $this->setResult($result);
    }


    protected function createReferenceEvaluator(): ReferenceEvaluator
    {
        return ReferenceEvaluatorTest::factory()
            ->setAdvancer($this->createAdvancer());
    }
}
