<?php

namespace Remorhaz\JSONPointer\Evaluator;

class LocatorEvaluatorDelete extends LocatorEvaluator
{


    protected function evaluateLocator()
    {
        throw new EvaluatorException("Only array items and object properties are deletable");
    }


    protected function createReferenceEvaluator(): ReferenceEvaluator
    {
        return ReferenceEvaluatorDelete::factory()
            ->setAdvancer($this->createAdvancer());
    }
}
