<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluator;

use Remorhaz\JSONPointer\Locator\Reference;

class LocatorEvaluatorRead extends LocatorEvaluator
{


    protected function processLocator()
    {
        return $this->setResult($this->getCursor()->getData());
    }

    
    protected function createReferenceEvaluator()
    {
        return ReferenceEvaluatorRead::factory()
            ->setAdvancer($this->createAdvancer());
    }
}
