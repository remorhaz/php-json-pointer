<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceReadProperty extends ReferenceAdvanceable
{


    protected function performNonExisting()
    {
        $this
            ->getAdvancer()
            ->fail();
    }
}
