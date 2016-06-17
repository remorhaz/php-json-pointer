<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceRead extends ReferenceAdvanceable
{

    protected function performNonExisting()
    {
        $this
            ->getAdvancer()
            ->fail();
    }
}
