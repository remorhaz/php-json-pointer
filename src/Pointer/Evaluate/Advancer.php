<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

abstract class Advancer
{

    /**
     * @var Cursor|null
     */
    protected $cursor;


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


    /**
     * @return $this
     * @throws EvaluateException
     */
    abstract public function fail();


    protected static function factory(Cursor $cursor = null)
    {
        $advancer = new static();
        if (null !== $cursor) {
            $advancer->setCursor($cursor);
        }
        return $advancer;
    }


    /**
     * @param Cursor $cursor
     * @return Advancer
     */
    final public static function byCursorFactory(Cursor $cursor)
    {
        if (is_object($cursor->getData())) {
            return AdvancerProperty::factory($cursor);
        }
        if (is_array($cursor->getData())) {
            $reference = $cursor->getReference();
            switch ($reference->getType()) {
                case $reference::TYPE_NEXT_INDEX:
                    return AdvancerNextIndex::factory($cursor);

                case $reference::TYPE_INDEX:
                    return AdvancerNumericIndex::factory($cursor);

                case $reference::TYPE_PROPERTY:
                    return AdvancerNonNumericIndex::factory($cursor);
            }
            $reference = $cursor->getReference();
            throw new DomainException(
                "Failed to create array index advancer for reference '{$reference->getText()}' " .
                "of type {$reference->getType()}"
            );
        }
        $reference = $cursor->getReference();
        throw new EvaluateException(
            "Cannot advance through non-structured data by reference '{$reference->getText()}'"
        );
    }


    /**
     * @param Cursor $cursor
     * @return $this
     */
    public function setCursor(Cursor $cursor)
    {
        $this->cursor = $cursor;
        return $this;
    }


    /**
     * @return Cursor
     */
    public function getCursor()
    {
        if (null === $this->cursor) {
            throw new LogicException("Cursor is not set in advancer");
        }
        return $this->cursor;
    }


    public function canWrite()
    {
        return $this
            ->getCursor()
            ->getReference()
            ->isLast();
    }


    public function getKey()
    {
        return $this
            ->getCursor()
            ->getReference()
            ->getValue();
    }


    public function getKeyDescription()
    {
        return "'{$this->getKey()}'";
    }
}
