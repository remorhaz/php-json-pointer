<?php

namespace Remorhaz\JSONPointer\Evaluator;

class ReferenceEvaluatorTest extends ReferenceEvaluator
{


    protected function doAdvanceCursorFail()
    {
        $result = false;
        return $this->setResult($result);
    }
}
