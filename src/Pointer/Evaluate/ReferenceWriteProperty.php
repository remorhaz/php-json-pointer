<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceWriteProperty extends ReferenceWrite
{


    protected function createAdvancer()
    {
        return AdvancerProperty::factory();
    }

    protected function performNonExisting()
    {
        if (!$this->canPerformNonExisting()) {
            $propertyDescription = $this
                ->getAdvancer()
                ->getValueDescription();
            throw new EvaluateException(
                "Cannot write to non-existing property '{$propertyDescription}' if it is not last"
            );
        }
        $this
            ->getAdvancer()
            ->write($this->getValue());
        $result = null;
        return $this->setResult($result);
    }


    protected function canPerformNonExisting()
    {
        return $this
            ->getReference()
            ->isLast();
    }
}
