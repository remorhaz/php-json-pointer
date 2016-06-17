<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class AdvancerNextIndex extends Advancer
{


    public function canAdvance()
    {
        return false;
    }


    public function advance()
    {
        throw new LogicException("New index is not advanceable");
    }


    public function write($data)
    {
        $this->dataCursor[] = $data;
        return $this;
    }


    public function fail()
    {
        throw new EvaluateException("Index {$this->getValueDescription()} is not found");
    }


    public function getValue()
    {
        throw new LogicException("Next index cannot have advanceable value");
    }


    public function getValueDescription()
    {
        return "'-'";
    }
}
