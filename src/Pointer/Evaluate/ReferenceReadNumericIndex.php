<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceReadNumericIndex extends ReferenceAdvanceable
{


    protected function performNonExisting()
    {
        $this
            ->getAdvancer()
            ->fail();
    }
}
