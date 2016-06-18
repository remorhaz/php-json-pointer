<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceRead extends ReferenceEvaluate
{

    protected function onCursorAdvanceFail()
    {
        $this
            ->getAdvancer()
            ->fail();
    }
}
