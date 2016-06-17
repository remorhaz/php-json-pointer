<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceTest extends ReferenceAdvanceable
{


    protected function performNonExisting()
    {
        $result = false;
        return $this->setResult($result);
    }
}
