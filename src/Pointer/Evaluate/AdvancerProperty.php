<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class AdvancerProperty extends Advancer
{


    public function canAdvance()
    {
        return property_exists($this->getDataCursor(), $this->getValue());
    }


    public function advance()
    {
        return $this->setNewDataCursor($this->dataCursor->{$this->getValue()});
    }


    public function write($data)
    {
        $this->dataCursor->{$this->getValue()} = $data;
        return $this;
    }


    public function fail()
    {
        throw new EvaluateException("Property {$this->getValueDescription()} is not found");
    }
}
