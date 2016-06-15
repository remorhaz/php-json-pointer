<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceTestNumericIndex extends ReferenceAdvanceable
{


    protected function createAdvancer()
    {
        return AdvancerNumericIndex::factory();
    }


    protected function performNonExisting()
    {
        $result = false;
        return $this->setResult($result);
    }
}
