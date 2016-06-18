<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class AdvancerProperty extends Advancer
{


    public function canAdvance()
    {
        return property_exists($this->getCursor()->getData(), $this->getKey());
    }


    protected function &advance(&$cursorData)
    {
        return $cursorData->{$this->getKey()};
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
