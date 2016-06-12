<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

class Write extends \Remorhaz\JSONPointer\Pointer\Evaluate
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


    protected function processCursor()
    {
        $this->cursor = $this->getValue();
        $result = null;
        return $this->setResult($result);
    }


    protected function processNextArrayIndex(Reference $reference)
    {
        if ($reference->isLast()) {
            $this->cursor[] = $this->getValue();
            $result = null;
            return $this->setResult($result);
        }
        throw new EvaluateException("Accessing non-existing index in array");
    }


    protected function processNonExistingObjectProperty(Reference $reference)
    {
        if ($reference->isLast()) {
            $this->cursor->{$reference->getValue()} = $this->getValue();
            $result = null;
            return $this->setResult($result);
        }
        throw new EvaluateException("Accessing non-existing property in object");
    }


    protected function processNonExistingArrayIndex(Reference $reference)
    {
        $index = $this->getArrayIndex($reference);
        if ($reference->isLast()) {
            $this->cursor[$index] = $this->getValue();
            $result = null;
            return $this->setResult($result);
        }
        var_export($this->cursor);
        var_export($index);
        $indexText = is_int($index) ? "{$index}" : "'{$index}'";
        throw new EvaluateException("Accessing non-existing index {$indexText} in array");
    }
}
