<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class AdvancerNonNumericIndex extends Advancer
{


    public function canAdvance()
    {
        return array_key_exists($this->getValue(), $this->getDataCursor());
    }


    public function advance()
    {
        return $this->setNewDataCursor($this->dataCursor[$this->getValue()]);
    }


    public function write($data)
    {
        $this->dataCursor[$this->getValue()] = $data;
        return $this;
    }
}
