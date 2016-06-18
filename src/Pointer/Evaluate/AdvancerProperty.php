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
        $cursorData = &$this
            ->getCursor()
            ->getData();
        $cursorData = &$cursorData->{$this->getKey()};
        $this
            ->getCursor()
            ->setData($cursorData);
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
