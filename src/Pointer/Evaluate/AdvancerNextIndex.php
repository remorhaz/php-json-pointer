<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class AdvancerNextIndex extends AdvancerIndex
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
        $this
            ->getCursor()
            ->getData()[] = $data;
        return $this;
    }


    public function getValue()
    {
        throw new LogicException("Next index don't have advanceable key");
    }


    public function getValueDescription()
    {
        return "'-'";
    }
}
