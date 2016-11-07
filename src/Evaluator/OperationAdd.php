<?php

namespace Remorhaz\JSON\Pointer\Evaluator;

use Remorhaz\JSON\Data\ReaderInterface;
use Remorhaz\JSON\Data\SelectableWriterInterface;
use Remorhaz\JSON\Pointer\Locator\Locator;
use Remorhaz\JSON\Pointer\Locator\Reference;

/**
 * Implements 'add' operation compliant with RFC-6902.
 */
class OperationAdd extends Operation
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
            if ($this->writer->isIndexSelected()) {
                $this->writer->insertElement($this->valueReader);
            } else {
                $this->writer->replaceData($this->valueReader);
            }
        } else {
            if ($this->writer->isNewIndexSelected()) {
                $this->writer->appendElement($this->valueReader);
            } elseif ($this->writer->isPropertySelected()) {
                $this->writer->insertProperty($this->valueReader);
            } else {
                throw new EvaluatorException("No data at {$this->locator->getText()}");
            }
        }
        return $this;
    }


    protected function applyReference(Reference $reference)
    {
        if (!$this->writer->hasData()) {
            throw new EvaluatorException("No data at '{$reference->getPath()}'");
        }
        if ($this->writer->isArraySelected()) {
            if ($reference->getType() == $reference::TYPE_NEXT_INDEX) {
                $this->writer->selectNewIndex();
            } elseif ($reference->getType() == $reference::TYPE_INDEX) {
                $index = (int) $reference->getKey();
                $elementCount = $this->writer->getElementCount();
                if ($index == $elementCount) {
                    $this->writer->selectNewIndex();
                } elseif ($index < $elementCount && $index >= 0) {
                    $this->writer->selectIndex($index);
                } else {
                    throw new EvaluatorException("Invalid index #{$index} at '{$reference->getPath()}'");
                }
            } else {
                throw new EvaluatorException("Invalid index '{$reference->getKey()}' at '{$reference->getPath()}'");
            }
        } elseif ($this->writer->isObjectSelected()) {
            $property = (string) $reference->getKey();
            $this->writer->selectProperty($property);
        } else {
            throw new EvaluatorException("Scalar data at '{$reference->getPath()}'");
        }
        return $this;
    }
}
