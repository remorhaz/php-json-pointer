<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceRead extends ReferenceEvaluate
{

    protected function performNonExisting()
    {
        $this
            ->getAdvancer()
            ->fail();
    }
}
