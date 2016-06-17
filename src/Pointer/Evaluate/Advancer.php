<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

abstract class Advancer
{


    protected $dataCursor;

    protected $isDataCursorSet = false;

    protected $newDataCursor;

    protected $isNewDataCursorSet = false;

    /**
     * @var Reference|null
     */
    protected $reference;


    protected function __construct()
    {
    }


    /**
     * @return bool
     */
    abstract public function canAdvance();

    /**
     * @return $this
     */
    abstract public function advance();


    /**
     * @param mixed $data
     * @return $this
     */
    abstract public function write($data);


    public function canWrite()
    {
        return $this
            ->getReference()
            ->isLast();
    }

    /**
     * @return $this
     * @throws EvaluateException
     */
    abstract public function fail();


    public static function factory()
    {
        return new static();
    }


    /**
     * @param Cursor $cursor
     * @return Advancer
     */
    final public static function byCursorFactory(Cursor $cursor)
    {
        if (is_object($cursor->getData())) {
            return AdvancerProperty::factory();
        }
        if (is_array($cursor->getData())) {
            $reference = $cursor->getReference();
            switch ($reference->getType()) {
                case $reference::TYPE_NEXT_INDEX:
                    return AdvancerNextIndex::factory();

                case $reference::TYPE_INDEX:
                    return AdvancerNumericIndex::factory();

                case $reference::TYPE_PROPERTY:
                    return AdvancerNonNumericIndex::factory();
            }
            $reference = $cursor->getReference();
            throw new DomainException(
                "Failed to create array index advancer for reference of type {$reference->getType()}"
            );
        }
        $reference = $cursor->getReference();
        throw new EvaluateException(
            "Cannot advance through non-structured data by reference '{$reference->getText()}'"
        );

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
            throw new LogicException("Data cursor is not set in advancer object");
        }
        return $this->dataCursor;
    }


    protected function setNewDataCursor(&$dataCursor)
    {
        $this->newDataCursor = &$dataCursor;
        $this->isNewDataCursorSet = true;
        return $this;
    }


    public function &getNewDataCursor()
    {
        if (!$this->isNewDataCursorSet) {
            throw new LogicException("Data cursor is not set in advancer object");
        }
        return $this->newDataCursor;
    }


    public function setReference(Reference $reference)
    {
        $this->reference = $reference;
        return $this;
    }


    protected function getReference()
    {
        if (null === $this->reference) {
            throw new LogicException("Reference is not set in advancer object");
        }
        return $this->reference;
    }


    public function getValue()
    {
        return $this
            ->getReference()
            ->getValue();
    }


    public function getValueDescription()
    {
        return "'{$this->getValue()}'";
    }
}
