<?php

namespace Remorhaz\JSONPointer\Data;

class RawCursor
{

    protected $data;

    protected $isBound = false;


    public function __construct()
    {
    }


    public function isBound(): bool
    {
        return $this->isBound;
    }


    public function bind(&$data)
    {
        $this
            ->unbind()
            ->data = &$data;
        $this->isBound = true;
        return $this;
    }


    public function unbind()
    {
        unset($this->data); // Unsets reference and removes data property.
        $this->data = null; // Restores data property to initial state.
        $this->isBound = false;
        return $this;
    }


    public function &getDataReference()
    {
        if (!$this->isBound()) {
            throw new LogicException("No data bound to cursor");
        }
        return $this->data;
    }


    public function getDataCopy()
    {
        $dataReference = &$this->getDataReference();
        return is_object($dataReference) ? clone $dataReference : $dataReference;
    }
}
