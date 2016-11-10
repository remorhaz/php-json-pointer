<?php

namespace Remorhaz\JSON\Pointer\Evaluator;

use Remorhaz\JSON\Data\Reference\Reader;
use Remorhaz\JSON\Data\SelectorInterface;
use Remorhaz\JSON\Pointer\Locator\Locator;
use Remorhaz\JSON\Pointer\Locator\Reference;

class OperationTest extends Operation
{

    protected $selector;


    public function __construct(Locator $locator, SelectorInterface $selector)
    {
        parent::__construct($locator);
        $this->selector = $selector;
    }


    public function perform()
    {
        parent::perform();
        if (!$this->isResultSet()) {
            $this->setResultFromBool(true);
        }
        return $this;
    }


    protected function applyReference(Reference $reference)
    {
        if ($this->selector->isArray()) {
            if ($reference->getType() != $reference::TYPE_INDEX) {
                return $this->setResultFromBool(false);
            }
            $index = (int) $reference->getKey();
            $hasData = $this
                ->selector
                ->selectElement($index)
                ->hasData();
            if (!$hasData) {
                return $this->setResultFromBool(false);
            }
        } elseif ($this->selector->isObject()) {
            $property = (string) $reference->getKey();
            $hasData = $this
                ->selector
                ->selectProperty($property)
                ->hasData();
            if (!$hasData) {
                return $this->setResultFromBool(false);
            }
        } else {
            return $this->setResultFromBool(false);
        }
        return $this;
    }


    private function setResultFromBool($resultData)
    {
        $result = new Reader($resultData);
        return $this->setResult($result);
    }
}