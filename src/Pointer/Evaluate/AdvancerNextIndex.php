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
        $cursorData = &$this
            ->getCursor()
            ->getData();
        $cursorData[] = $data;
        return $this;
    }


    public function getKey()
    {
        throw new LogicException("Next index don't have advanceable key");
    }


    public function getKeyDescription()
    {
        return "'-'";
    }
}
