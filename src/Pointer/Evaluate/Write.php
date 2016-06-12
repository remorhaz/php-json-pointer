<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

class Write extends \Remorhaz\JSONPointer\Pointer\Evaluate
{

    protected $value;

    protected $isValueSet = false;

    protected $numericIndexGaps = false;


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


    public function setNumericIndexGaps($allow = true)
    {
        if (!is_bool($allow)) {
            throw new InvalidArgumentException("Write any index flag must be boolean");
        }
        $this->numericIndexGaps = $allow;
        return $this;
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
            $canWrite = is_int($index)
                ? 0 == $index || array_key_exists($index - 1, $this->cursor) || $this->numericIndexGaps
                : $this->nonNumericIndices;
            if ($canWrite) {
                $this->cursor[$index] = $this->getValue();
                $result = null;
                return $this->setResult($result);
            }
        }
        $indexText = is_int($index) ? "{$index}" : "'{$index}'";
        throw new EvaluateException("Accessing non-existing index {$indexText} in array");
    }
}
