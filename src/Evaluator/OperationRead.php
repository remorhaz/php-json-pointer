<?php

namespace Remorhaz\JSON\Pointer\Evaluator;

use Remorhaz\JSON\Data\SelectableReaderInterface;
use Remorhaz\JSON\Pointer\Locator\Locator;
use Remorhaz\JSON\Pointer\Locator\Reference;

class OperationRead extends Operation
{

    protected $reader;


    public function __construct(Locator $locator, SelectableReaderInterface $reader)
    {
        parent::__construct($locator);
        $this->reader = $reader;
    }


    public function perform()
    {
        return parent::perform()
            ->setResult($this->reader->getData());
    }


    protected function applyReference(Reference $reference)
    {
        if ($this->reader->isArraySelected()) {
            if ($reference->getType() != $reference::TYPE_INDEX) {
                throw new EvaluatorException(
                    "Invalid index '{$reference->getKey()}' at {$reference->getPath()}"
                );
            }
            $index = (int) $reference->getKey();
            $hasData = $this
                ->reader
                ->selectIndex($index)
                ->hasData();
            if (!$hasData) {
                throw new EvaluatorException("No index #{$index} at {$reference->getPath()}");
            }
        } elseif ($this->reader->isObjectSelected()) {
            $property = (string) $reference->getKey();
            $hasData = $this
                ->reader
                ->selectProperty($property)
                ->hasData();
            if (!$hasData) {
                throw new EvaluatorException("No property '{$property}' at {$reference->getPath()}");
            }
        } else {
            throw new EvaluatorException("Scalar data at {$reference->getPath()}");
        }
        return $this;
    }
}
