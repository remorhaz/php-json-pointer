<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class AdvancerProperty extends Advancer
{


    public function canAdvance()
    {
        return property_exists($this->getCursor()->getData(), $this->getKey());
    }


    public function advance()
    {
        $data = &$this
            ->getCursor()
            ->getData()
            ->{$this->getKey()};
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
            ->{$this->getKey()} = $data;
        return $this;
    }


    public function fail()
    {
        throw new EvaluateException("Property {$this->getKeyDescription()} is not found");
    }
}
