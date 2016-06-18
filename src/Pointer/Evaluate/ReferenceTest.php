<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceTest extends ReferenceEvaluate
{


    protected function onCursorAdvanceFail()
    {
        $result = false;
        return $this->setResult($result);
    }
}
