<?php

namespace Remorhaz\JSON\Pointer\Evaluator;

use Remorhaz\JSON\Data\ReaderInterface;
use Remorhaz\JSON\Data\WriterInterface;
use Remorhaz\JSON\Pointer\Locator\Locator;
use Remorhaz\JSON\Pointer\Locator\Reference;

/**
 * Implements 'add' operation compliant with RFC-6902.
 */
class OperationAdd extends Operation
{

    protected $writer;

    protected $valueReader;


    public function __construct(Locator $locator, WriterInterface $writer, ReaderInterface $valueReader)
    {
        parent::__construct($locator);
        $this->writer = $writer;
        $this->valueReader = $valueReader;
    }


    public function perform()
    {
        parent::perform();
        if ($this->writer->hasData()) {
            if ($this->writer->isElement()) {
                $this->writer->insertElement($this->valueReader);
            } else {
                $this->writer->replaceData($this->valueReader);
            }
        } else {
            if ($this->writer->isNewElement()) {
                $this->writer->appendElement($this->valueReader);
            } elseif ($this->writer->isProperty()) {
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
        if ($this->writer->isArray()) {
            if ($reference->getType() == $reference::TYPE_NEXT_INDEX) {
                $this->writer->selectNewElement();
            } elseif ($reference->getType() == $reference::TYPE_INDEX) {
                $index = (int) $reference->getKey();
                $elementCount = $this->writer->getElementCount();
                if ($index == $elementCount) {
                    $this->writer->selectNewElement();
                } elseif ($index < $elementCount && $index >= 0) {
                    $this->writer->selectElement($index);
                } else {
                    throw new EvaluatorException("Invalid index #{$index} at '{$reference->getPath()}'");
                }
            } else {
                throw new EvaluatorException("Invalid index '{$reference->getKey()}' at '{$reference->getPath()}'");
            }
        } elseif ($this->writer->isObject()) {
            $property = (string) $reference->getKey();
            $this->writer->selectProperty($property);
        } else {
            throw new EvaluatorException("Scalar data at '{$reference->getPath()}'");
        }
        return $this;
    }
}
