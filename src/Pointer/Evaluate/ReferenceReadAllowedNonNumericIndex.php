<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceReadAllowedNonNumericIndex extends ReferenceAdvanceable
{


    protected function createAdvancer()
    {
        return AdvancerNonNumericIndex::factory();
    }


    protected function performNonExisting()
    {
        $indexDescription = $this
            ->getAdvancer()
            ->getValueDescription();
        throw new EvaluateException("No index {$indexDescription} in array");
    }
}
