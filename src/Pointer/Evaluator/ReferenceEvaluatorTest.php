<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluator;

class ReferenceEvaluatorTest extends ReferenceEvaluator
{


    protected function onCursorAdvanceFail()
    {
        $result = false;
        return $this->setResult($result);
    }
}
