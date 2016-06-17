<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceReadAllowedNonNumericIndex extends ReferenceAdvanceable
{


    protected function performNonExisting()
    {
        $this
            ->getAdvancer()
            ->fail();
    }
}
