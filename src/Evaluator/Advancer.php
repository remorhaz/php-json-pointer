<?php

namespace Remorhaz\JSONPointer\Evaluator;

abstract class Advancer
{

    /**
     * @var Cursor|null
     */
    protected $cursor;

    protected $isAdvanced;


    protected function __construct()
    {
    }


    /**
     * @return bool
     */
    abstract public function canAdvance();

    /**
     * @param mixed $cursorData
     * @return $this
     */
    abstract protected function &advance(&$cursorData);


    /**
     * @param mixed $data
     * @return $this
     */
    abstract public function write($data);


    /**
     * @return $this
     */
    abstract public function delete();


    /**
     * @return $this
     * @throws EvaluatorException
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
        return AdvancerScalar::factory($cursor);
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


    public function advanceCursor()
    {
        if (null !== $this->isAdvanced) {
            throw new LogicException("Advance is already performed");
        }
        if (!$this->canAdvance()) {
            throw new LogicException("Advance cannot be performed");
        }
        $cursorData = &$this
            ->getCursor()
            ->getData();
        $cursorData = &$this->advance($cursorData);
        $this
            ->getCursor()
            ->setData($cursorData);
        $this->isAdvanced = true;
        return $this;
    }


    public function isAdvanced()
    {
        if (null === $this->isAdvanced) {
            throw new LogicException("Advance flag is not set");
        }
        return $this->isAdvanced;
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
            ->getKey();
    }


    public function getKeyDescription()
    {
        return "'{$this->getKey()}'";
    }
}
