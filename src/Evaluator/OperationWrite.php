<?php

namespace Remorhaz\JSONPointer\Evaluator;

use Remorhaz\JSON\Data\ReaderInterface;
use Remorhaz\JSON\Data\SelectableWriterInterface;
use Remorhaz\JSONPointer\Locator\Locator;
use Remorhaz\JSONPointer\Locator\Reference;

class OperationWrite extends Operation
{

    protected $writer;

    protected $valueReader;


    public function __construct(Locator $locator, SelectableWriterInterface $writer, ReaderInterface $valueReader)
    {
        parent::__construct($locator);
        $this->writer = $writer;
        $this->valueReader = $valueReader;
    }


    public function perform()
    {
        parent::perform();
        if ($this->writer->hasData()) {
            $this->writer->replaceData($this->valueReader);
        } else {
            if ($this->writer->isNewIndexSelected()) {
                $this->writer->appendElement($this->valueReader);
            } elseif ($this->writer->isIndexSelected()) {
                throw new EvaluatorException("No data at {$this->locator->getText()}");
            } elseif ($this->writer->isPropertySelected()) {
                $this->writer->insertProperty($this->valueReader);
            } else {
                throw new EvaluatorException("Failed to write data at {$this->locator->getText()}");
            }
        }
        return $this;
    }


    protected function applyReference(Reference $reference)
    {
        if (!$this->writer->hasData()) {
            throw new EvaluatorException("No data at {$reference->getPath()}");
        }
        if ($this->writer->isArraySelected()) {
            if ($reference->getType() == $reference::TYPE_NEXT_INDEX) {
                $this->writer->selectNewIndex();
            } elseif ($reference->getType() == $reference::TYPE_INDEX) {
                $index = (int) $reference->getKey();
                $this->writer->selectIndex($index);
            } else {
                throw new EvaluatorException("Invalid index '{$reference->getKey()}' at {$reference->getPath()}");
            }
        } elseif ($this->writer->isObjectSelected()) {
            $property = (string) $reference->getKey();
            $this->writer->selectProperty($property);
        } else {
            throw new EvaluatorException("Scalar data at {$reference->getPath()}");
        }
        return $this;
    }
}
