<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceTestAllowedNonNumericIndex extends ReferenceAdvanceable
{


    protected function performNonExisting()
    {
        $result = false;
        return $this->setResult($result);
    }
}
