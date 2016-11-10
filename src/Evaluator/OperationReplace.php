<?php

namespace Remorhaz\JSON\Pointer\Evaluator;

use Remorhaz\JSON\Data\ReaderInterface;
use Remorhaz\JSON\Data\WriterInterface;
use Remorhaz\JSON\Pointer\Locator\Locator;
use Remorhaz\JSON\Pointer\Locator\Reference;

/**
 * Implements 'replace' operation compliant with RFC-6902.
 */
class OperationReplace extends Operation
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
        $this->writer->replaceData($this->valueReader);
        return $this;
    }


    protected function applyReference(Reference $reference)
    {
        if ($this->writer->isArray()) {
            if ($reference->getType() != $reference::TYPE_INDEX) {
                throw new EvaluatorException("Invalid index '{$reference->getKey()}' at '{$reference->getPath()}'");
            }
            $index = (int) $reference->getKey();
            $this->writer->selectElement($index);
            if (!$this->writer->hasData()) {
                throw new EvaluatorException("No element #{$index} at '{$reference->getPath()}'");
            }
        } elseif ($this->writer->isObject()) {
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
