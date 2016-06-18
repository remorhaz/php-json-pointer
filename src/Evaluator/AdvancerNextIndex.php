<?php

namespace Remorhaz\JSONPointer\Evaluator;

class AdvancerNextIndex extends AdvancerIndex
{


    public function canAdvance()
    {
        return false;
    }


    protected function &advance(&$cursorData)
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
