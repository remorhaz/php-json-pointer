<?php

namespace Remorhaz\JSONPointer\Evaluator;

class ReferenceEvaluatorTest extends ReferenceEvaluator
{


    protected function onCursorAdvanceFail()
    {
        $result = false;
        return $this->setResult($result);
    }
}
