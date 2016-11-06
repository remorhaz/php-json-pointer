<?php

namespace Remorhaz\JSONPointer\Evaluator;

use Remorhaz\JSON\Data\SelectableWriterInterface;
use Remorhaz\JSONPointer\Locator\Locator;
use Remorhaz\JSONPointer\Locator\Reference;

class OperationDelete extends Operation
{

    protected $writer;


    public function __construct(Locator $locator, SelectableWriterInterface $writer)
    {
        parent::__construct($locator);
        $this->writer = $writer;
    }


    public function perform()
    {
        if (empty($this->locator->getReferenceList())) {
            throw new EvaluatorException("Data root can't be deleted");
        }
        parent::perform();
        if ($this->writer->isIndexSelected()) {
            $this->writer->removeElement();
        } elseif ($this->writer->isPropertySelected()) {
            $this->writer->removeProperty();
        } else {
            throw new LogicException("Failed to remove data at '{$this->locator->getText()}'");
        }
        return $this;
    }


    protected function applyReference(Reference $reference)
    {
        if ($this->writer->isArraySelected()) {
            if ($reference->getType() != $reference::TYPE_INDEX) {
                throw new EvaluatorException(
                    "Invalid index '{$reference->getKey()}' at '{$reference->getPath()}''"
                );
            }
            $index = (int) $reference->getKey();
            $this->writer->selectIndex($index);
            if (!$this->writer->hasData()) {
                throw new EvaluatorException("No element #{$index} at '{$reference->getPath()}'");
            }
        } elseif ($this->writer->isObjectSelected()) {
            $property = (string) $reference->getKey();
            $this->writer->selectProperty($property);
            if (!$this->writer->hasData()) {
                throw new EvaluatorException("No property '{$property}' at '{$reference->getPath()}'");
            }
        } else {
            throw new EvaluatorException("Scalar data at '{$reference->getPath()}'");
        }
        return $this;
    }
}
