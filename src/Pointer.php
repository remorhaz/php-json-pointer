<?php

namespace Remorhaz\JSONPointer;

use Remorhaz\JSONPointer\Data\Raw;
use Remorhaz\JSONPointer\Locator\Locator;
use Remorhaz\JSONPointer\Parser\Parser;

class Pointer
{

    /**
     * JSON Pointer string buffer.
     *
     * @var string|null
     */
    protected $text;

    /**
     * Data for evaluation.
     *
     * @var mixed
     */
    protected $data;

    /**
     * Internal flag of data being set.
     *
     * @var bool
     */
    protected $isDataSet = false;

    /**
     * Locator buffer.
     *
     * @var Locator|null
     */
    protected $locator;

    /**
     * Parser object.
     *
     * @var Parser|null
     */
    protected $parser;


    /**
     * Constructor.
     */
    protected function __construct()
    {
    }


    /**
     * Creates object instance.
     *
     * @return static
     */
    public static function factory()
    {
        return new static();
    }


    /**
     * Sets JSON Pointer string.
     *
     * @param string $text
     * @return $this
     */
    public function setText(string $text)
    {
        if ($this->text !== $text) {
            $this->text = $text;
            $this->locator = null;
        }
        return $this;
    }


    /**
     * Returns JSON Pointer string.
     *
     * @return string
     * @throws LogicException
     */
    public function getText(): string
    {
        if (null === $this->text) {
            throw new LogicException("JSON Pointer text is not set");
        }
        return $this->text;
    }


    public function setData(&$data)
    {
        $this->data = &$data;
        $this->isDataSet = true;
        return $this;
    }


    protected function &getData()
    {
        if (!$this->isDataSet) {
            throw new LogicException("Data for evaluation is not set");
        }
        return $this->data;
    }


    public function test(): bool
    {
        $reader = new Raw($this->getData());
        $referenceList = $this
            ->getLocator()
            ->getReferenceList();
        foreach ($referenceList as $reference) {
            if ($reader->isArraySelected()) {
                if ($reference->getType() != $reference::TYPE_INDEX) {
                    return false;
                }
                $index = (int) $reference->getKey();
                $reader->selectIndex($index);
                if (!$reader->hasData()) {
                    return false;
                }
            } elseif ($reader->isObjectSelected()) {
                $property = (string) $reference->getKey();
                $reader->selectProperty($property);
                if (!$reader->hasData()) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }


    public function read()
    {
        $reader = new Raw($this->getData());
        $referenceList = $this
            ->getLocator()
            ->getReferenceList();
        foreach ($referenceList as $reference) {
            if ($reader->isArraySelected()) {
                if ($reference->getType() != $reference::TYPE_INDEX) {
                    throw new Evaluator\EvaluatorException(
                        "Invalid index '{$reference->getKey()}' at {$reference->getPath()}"
                    );
                }
                $index = (int) $reference->getKey();
                $reader->selectIndex($index);
                if (!$reader->hasData()) {
                    throw new Evaluator\EvaluatorException("No index #{$index} at {$reference->getPath()}");
                }
            } elseif ($reader->isObjectSelected()) {
                $property = (string) $reference->getKey();
                $reader->selectProperty($property);
                if (!$reader->hasData()) {
                    throw new Evaluator\EvaluatorException("No property '{$property}' at {$reference->getPath()}");
                }
            } else {
                throw new Evaluator\EvaluatorException("Scalar data at {$reference->getPath()}");
            }
        }
        return $reader->getData();
    }


    public function write($value)
    {
        $writer = new Raw($this->getData());
        $referenceList = $this
            ->getLocator()
            ->getReferenceList();
        foreach ($referenceList as $reference) {
            if (!$writer->hasData()) {
                throw new Evaluator\EvaluatorException("No data at {$reference->getPath()}");
            }
            if ($writer->isArraySelected()) {
                if ($reference->getType() == $reference::TYPE_NEXT_INDEX) {
                    $writer->selectNewIndex();
                } elseif ($reference->getType() == $reference::TYPE_INDEX) {
                    $index = (int) $reference->getKey();
                    $writer->selectIndex($index);
                } else {
                    throw new Evaluator\EvaluatorException(
                        "Invalid index '{$reference->getKey()}' at {$reference->getPath()}"
                    );
                }
            } elseif ($writer->isObjectSelected()) {
                $property = (string) $reference->getKey();
                $writer->selectProperty($property);
            } else {
                throw new Evaluator\EvaluatorException("Scalar data at {$reference->getPath()}");
            }
        }
        $valueReader = new Raw($value);
        if ($writer->hasData()) {
            $writer->replaceData($valueReader);
        } else {
            if ($writer->isNewIndexSelected()) {
                $writer->appendElement($valueReader);
            } elseif ($writer->isIndexSelected()) {
                throw new Evaluator\EvaluatorException("No data at {$this->getText()}");
            } elseif ($writer->isPropertySelected()) {
                $writer->insertProperty($valueReader);
            } else {
                throw new Evaluator\EvaluatorException("Failed to write data at {$this->getText()}");
            }
        }
        return $this;
    }


    public function delete()
    {
        $writer = new Raw($this->getData());
        $referenceList = $this
            ->getLocator()
            ->getReferenceList();
        if (empty($referenceList)) {
            throw new Evaluator\EvaluatorException("Data root can't be deleted");
        }
        foreach ($referenceList as $reference) {
            if ($writer->isArraySelected()) {
                if ($reference->getType() != $reference::TYPE_INDEX) {
                    throw new Evaluator\EvaluatorException(
                        "Invalid index '{$reference->getKey()}' at '{$reference->getPath()}''"
                    );
                }
                $index = (int) $reference->getKey();
                $writer->selectIndex($index);
                if (!$writer->hasData()) {
                    throw new Evaluator\EvaluatorException("No element #{$index} at '{$reference->getPath()}'");
                }
            } elseif ($writer->isObjectSelected()) {
                $property = (string) $reference->getKey();
                $writer->selectProperty($property);
                if (!$writer->hasData()) {
                    throw new Evaluator\EvaluatorException("No property '{$property}' at '{$reference->getPath()}'");
                }
            } else {
                throw new Evaluator\EvaluatorException("Scalar data at '{$reference->getPath()}'");
            }
        }
        if ($writer->isIndexSelected()) {
            $writer->removeElement();
        } elseif ($writer->isPropertySelected()) {
            $writer->removeProperty();
        } else {
            throw new Evaluator\LogicException("Failed to remove data at '{$this->getText()}'");
        }
        return $this;
    }


    /**
     * Returns parser object.
     *
     * @return Parser
     */
    protected function getParser(): Parser
    {
        if (null === $this->parser) {
            $this->parser = Parser::factory();
        }
        return $this->parser;
    }


    /**
     * Returns locator built from source text.
     *
     * @return Locator
     */
    protected function getLocator(): Locator
    {
        if (null === $this->locator) {
            $this->locator = $this
                ->getParser()
                ->setText($this->getText())
                ->getLocator();
        }
        return $this->locator;
    }
}
