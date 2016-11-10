<?php

namespace Remorhaz\JSON\Pointer\Evaluator;

use Remorhaz\JSON\Data\SelectorInterface;
use Remorhaz\JSON\Pointer\Locator\Locator;
use Remorhaz\JSON\Pointer\Locator\Reference;

class OperationRead extends Operation
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
        return $this->setResult($this->selector->getAsReader());
    }


    protected function applyReference(Reference $reference)
    {
        if ($this->selector->isArray()) {
            if ($reference->getType() != $reference::TYPE_INDEX) {
                throw new EvaluatorException(
                    "Invalid index '{$reference->getKey()}' at {$reference->getPath()}"
                );
            }
            $index = (int) $reference->getKey();
            $this->selector->selectElement($index);
            if (!$this->selector->hasData()) {
                throw new EvaluatorException("No index #{$index} at {$reference->getPath()}");
            }
        } elseif ($this->selector->isObject()) {
            $property = (string) $reference->getKey();
            $this->selector->selectProperty($property);
            if (!$this->selector->hasData()) {
                throw new EvaluatorException("No property '{$property}' at {$reference->getPath()}");
            }
        } else {
            throw new EvaluatorException("Scalar data at {$reference->getPath()}");
        }
        return $this;
    }
}
