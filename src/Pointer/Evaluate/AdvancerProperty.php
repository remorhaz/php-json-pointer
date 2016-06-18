<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class AdvancerProperty extends Advancer
{


    public function canAdvance()
    {
        return property_exists($this->getCursor()->getData(), $this->getValue());
    }


    public function advance()
    {
        $data = &$this
            ->getCursor()
            ->getData()
            ->{$this->getValue()};
        $this
            ->getCursor()
            ->setData($data);
        return $this;
    }


    public function write($data)
    {
        $this
            ->getCursor()
            ->getData()
            ->{$this->getValue()} = $data;
        return $this;
    }


    public function fail()
    {
        throw new EvaluateException("Property {$this->getValueDescription()} is not found");
    }
}
