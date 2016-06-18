<?php

namespace Remorhaz\JSONPointer\Evaluator;

class ReferenceEvaluatorRead extends ReferenceEvaluator
{

    protected function onCursorAdvanceFail()
    {
        $this
            ->getAdvancer()
            ->fail();
    }
}
