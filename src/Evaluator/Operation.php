<?php

namespace Remorhaz\JSONPointer\Evaluator;

use Remorhaz\JSONPointer\Locator\Locator;
use Remorhaz\JSONPointer\Locator\Reference;

abstract class Operation
{

    protected $locator;

    protected $result;

    protected $isResultSet = false;


    public function __construct(Locator $locator)
    {
        $this->locator = $locator;
    }


    /**
     * Tries to apply given reference to data selector.
     *
     * @param Reference $reference
     * @return $this
     */
    abstract protected function applyReference(Reference $reference);


    /**
     * @return $this
     */
    public function perform()
    {
        foreach ($this->locator->getReferenceList() as $reference) {
            $this->applyReference($reference);
            if ($this->isResultSet()) {
                break;
            }
        }
        return $this;
    }


    public function getResult()
    {
        if (!$this->isResultSet()) {
            throw new LogicException("Operation result is not set");
        }
        return $this->result;
    }


    protected function setResult($result)
    {
        $this->result = $result;
        $this->isResultSet = true;
        return $this;
    }


    protected function isResultSet(): bool
    {
        return $this->isResultSet;
    }
}
