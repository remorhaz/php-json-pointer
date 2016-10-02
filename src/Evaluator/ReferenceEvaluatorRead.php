<?php

namespace Remorhaz\JSONPointer\Evaluator;

class ReferenceEvaluatorRead extends ReferenceEvaluator
{


    protected function doAdvanceCursorFail()
    {
        $this
            ->getAdvancer()
            ->fail();
        return $this;
    }
}
