<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceReadProperty extends ReferenceAdvanceable
{


    protected function createAdvancer()
    {
        return AdvancerProperty::factory();
    }


    protected function performNonExisting()
    {
        $propertyDescription = $this
            ->getAdvancer()
            ->getValueDescription();
        throw new EvaluateException("No property {$propertyDescription} in object");
    }
}
