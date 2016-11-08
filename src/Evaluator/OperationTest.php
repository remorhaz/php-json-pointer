<?php

namespace Remorhaz\JSON\Pointer\Evaluator;

use Remorhaz\JSON\Data\RawSelectableReader;
use Remorhaz\JSON\Data\SelectableReaderInterface;
use Remorhaz\JSON\Pointer\Locator\Locator;
use Remorhaz\JSON\Pointer\Locator\Reference;

class OperationTest extends Operation
{

    protected $reader;


    public function __construct(Locator $locator, SelectableReaderInterface $reader)
    {
        parent::__construct($locator);
        $this->reader = $reader;
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
        if ($this->reader->isArraySelected()) {
            if ($reference->getType() != $reference::TYPE_INDEX) {
                return $this->setResultFromBool(false);
            }
            $index = (int) $reference->getKey();
            $hasData = $this
                ->reader
                ->selectIndex($index)
                ->hasData();
            if (!$hasData) {
                return $this->setResultFromBool(false);
            }
        } elseif ($this->reader->isObjectSelected()) {
            $property = (string) $reference->getKey();
            $hasData = $this
                ->reader
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
        $result = new RawSelectableReader($resultData);
        return $this->setResult($result);
    }
}