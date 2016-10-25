<?php

namespace Remorhaz\JSONPointer\Evaluator;

class AdvancerNextIndex extends AdvancerIndex
{


    public function canAdvance(): bool
    {
        return false;
    }


    protected function &advance(&$cursorData)
    {
        throw new LogicException("New array index is not advanceable");
    }


    public function write($data)
    {
        $cursorData = &$this
            ->getCursor()
            ->getData();
        $cursorData[] = $data;
        return $this;
    }


    public function delete()
    {
        throw new LogicException("Next index can't point at deletable data");
    }


    public function getKey()
    {
        throw new LogicException("Next index don't have advanceable key");
    }


    public function getKeyDescription(): string
    {
        return "'-'";
    }
}
