<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

class ReferenceWrite extends ReferenceAdvanceable
{

    protected $value;

    protected $isValueSet = false;


    public function setValue($value)
    {
        $this->value = $value;
        $this->isValueSet = true;
        return $this;
    }


    protected function getValue()
    {
        if (!$this->isValueSet) {
            throw new LogicException("Value is not set in evaluator");
        }
        return $this->value;
    }


    protected function performNonExisting()
    {
        $canWrite = $this
            ->getAdvancer()
            ->canWrite();
        return $canWrite
            ? $this->write()
            : $this->failWrite();
    }


    protected function write()
    {
        $this
            ->getAdvancer()
            ->write($this->getValue());
        $result = null;
        return $this->setResult($result);
    }


    protected function failWrite()
    {
        $this
            ->getAdvancer()
            ->fail();
        return $this;
    }
}
