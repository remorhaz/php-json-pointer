<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluator;

class ReferenceEvaluatorRead extends ReferenceEvaluator
{

    protected function onCursorAdvanceFail()
    {
        $this
            ->getAdvancer()
            ->fail();
    }
}
