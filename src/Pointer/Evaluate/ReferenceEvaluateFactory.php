<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

abstract class ReferenceEvaluateFactory
{

    /**
     * @var Reference|null
     */
    protected $reference;

    /**
     * @var mixed
     */
    protected $dataCursor;

    protected $isDataCursorSet = false;

    /**
     * @var bool
     */
    protected $nonNumericIndices = false;


    protected function __construct()
    {
    }


    public static function factory()
    {
        return new static();
    }


    /**
     * @param Reference $reference
     * @return $this
     */
    public function setReference(Reference $reference)
    {
        $this->reference = $reference;
        return $this;
    }


    /**
     * @return Reference
     */
    protected function getReference()
    {
        if (null === $this->reference) {
            throw new LogicException("Reference is not set");
        }
        return $this->reference;
    }


    public function setDataCursor(&$dataCursor)
    {
        $this->dataCursor = &$dataCursor;
        $this->isDataCursorSet = true;
        return $this;
    }


    protected function &getDataCursor()
    {
        if (!$this->isDataCursorSet) {
            throw new LogicException("Data cursor is not set");
        }
        return $this->dataCursor;
    }


    public function allowNonNumericIndices()
    {
        $this->nonNumericIndices = true;
        return $this;
    }


    public function forbidNonNumericIndices()
    {
        $this->nonNumericIndices = false;
        return $this;
    }


    /**
     * @return ReferenceEvaluate
     */
    public function createEvaluate()
    {
        return $this
            ->doCreate()
            ->setReference($this->getReference())
            ->setDataCursor($this->getDataCursor());
    }


    /**
     * @return ReferenceEvaluate
     */
    protected function doCreate()
    {
        if (is_object($this->dataCursor)) {
            return $this->createProperty();
        }
        if (is_array($this->dataCursor)) {
            return $this->createIndex();
        }
        return $this->createReferenceScalar();
    }


    abstract protected function createProperty();


    protected function createIndex()
    {
        $reference = $this->getReference();
        switch ($reference->getType()) {
            case $reference::TYPE_NEXT_INDEX:
                return $this->createNextIndex();

            case $reference::TYPE_INDEX:
                return $this->createNumericIndex();

            case $reference::TYPE_PROPERTY:
                return $this->createNonNumericIndex();
        }
        return $this->createUnknownIndex();
    }


    abstract protected function createNextIndex();


    abstract protected function createNumericIndex();


    protected function createNonNumericIndex()
    {
        return $this->nonNumericIndices
            ? $this->createAllowedNonNumericIndex()
            : $this->createNotAllowedNonNumericIndex();
    }


    abstract protected function createAllowedNonNumericIndex();


    abstract protected function createNotAllowedNonNumericIndex();


    abstract protected function createUnknownIndex();


    abstract protected function createReferenceScalar();
}
