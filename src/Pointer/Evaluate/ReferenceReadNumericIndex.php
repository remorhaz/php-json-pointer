<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceReadNumericIndex extends ReferenceAdvanceable
{


    protected function createAdvancer()
    {
        return AdvancerNumericIndex::factory();
    }


    protected function performNonExisting()
    {
        $indexDescription = $this
            ->getAdvancer()
            ->getValueDescription();
        throw new EvaluateException("No index {$indexDescription} in array");
    }
}
