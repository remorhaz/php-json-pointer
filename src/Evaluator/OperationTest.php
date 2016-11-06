<?php

namespace Remorhaz\JSONPointer\Evaluator;

use Remorhaz\JSONPointer\Data\SelectableReaderInterface;
use Remorhaz\JSONPointer\Locator\Locator;
use Remorhaz\JSONPointer\Locator\Reference;

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
            $this->setResult(true);
        }
        return $this;
    }


    protected function applyReference(Reference $reference)
    {
        if ($this->reader->isArraySelected()) {
            if ($reference->getType() != $reference::TYPE_INDEX) {
                return $this->setResult(false);
            }
            $index = (int) $reference->getKey();
            $hasData = $this
                ->reader
                ->selectIndex($index)
                ->hasData();
            if (!$hasData) {
                return $this->setResult(false);
            }
        } elseif ($this->reader->isObjectSelected()) {
            $property = (string) $reference->getKey();
            $hasData = $this
                ->reader
                ->selectProperty($property)
                ->hasData();
            if (!$hasData) {
                return $this->setResult(false);
            }
        } else {
            return $this->setResult(false);
        }
        return $this;
    }
}