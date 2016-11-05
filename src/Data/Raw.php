<?php

namespace Remorhaz\JSONPointer\Data;

class Raw implements WriterInterface, SelectorInterface
{

    /**
     * Link to the root of raw PHP data structure.
     *
     * @var mixed
     */
    protected $root;

    /**
     * @var RawCursor
     */
    protected $cursor;

    /**
     * @var RawCursor
     */
    protected $parentCursor;

    protected $cursorKey;


    public function __construct(&$data)
    {
        $this->root = &$data;
        $this->selectRoot();
    }


    public function hasData(): bool
    {
        return $this
            ->getCursor()
            ->isBound();
    }


    public function getData()
    {
        return $this
            ->getCursor()
            ->getDataCopy();
    }


    public function replaceData(ReaderInterface $source)
    {
        if (!$this->hasData()) {
            throw new LogicException("No data to get replaced");
        }
        $targetData = &$this
            ->getCursor()
            ->getDataReference();
        $targetData = $source->getData();
        return $this;
    }


    public function insertProperty(ReaderInterface $source)
    {
        if (!$this->isPropertySelected()) {
            throw new LogicException("Property must be selected to insert data");
        }
        if ($this->hasData()) {
            throw new LogicException("Property already exists");
        }
        $sourceData = $source->getData();
        $parentData = &$this
            ->getParentCursor()
            ->getDataReference();
        $parentData->{$this->getProperty()} = &$sourceData;
        $this->getCursor()->bind($sourceData);
        return $this;
    }


    public function appendElement(ReaderInterface $source)
    {
        if (!$this->isNewIndexSelected()) {
            throw new LogicException("New index must be selected to insert data");
        }
        if ($this->hasData()) {
            throw new LogicException("Element already exists");
        }
        $sourceData = $source->getData();
        $parentData = &$this
            ->getParentCursor()
            ->getDataReference();
        $parentData[] = &$sourceData;
        array_splice($parentData, 0, 0); // Rebuilding array indices.
        $this
            ->getCursor()
            ->unbind();
        return $this;
    }

    /**
     * @return $this
     * @see AccessorInterface
     */
    public function removeProperty()
    {
        if (!$this->isPropertySelected()) {
            throw new LogicException("Property must be selected to be removed");
        }
        $property = $this->getProperty();
        if (!$this->hasData()) {
            throw new LogicException("Property '{$property}' doesn't have data");
        }
        $parentData = &$this
            ->getParentCursor()
            ->getDataReference();
        if (isset($parentData->{$property})) {
            unset($parentData->{$property});
        } else {
            if ($this->isNumericProperty($property)) {
                // Numeric properties exported from array can be accessed only through iteration in PHP.
                $newParentData = new \stdClass;
                foreach ($parentData as $existingProperty => &$value) {
                    if ($existingProperty != $property) {
                        $newParentData->{$existingProperty} = &$value;
                    }
                }
                unset($value);
                $parentData = $newParentData;
            }
        }
        $this->getCursor()->unbind();
        return $this;
    }


    public function removeElement()
    {
        if (!$this->isIndexSelected()) {
            throw new LogicException("Index must be selected to remove element");
        }
        $index = $this->getIndex();
        if (!$this->hasData()) {
            throw new LogicException("Element #{$index} doesn't have data");
        }
        $parentData = &$this
            ->getParentCursor()
            ->getDataReference();
        array_splice($parentData, $index, 1);
        $this
            ->unsetCursorKey()
            ->getCursor()
            ->unbind();
        return $this;
    }


    /**
     * @return $this
     * @see SelectableAccessorInterface
     */
    public function selectRoot()
    {
        $this->cursorKey = null;
        $this->getParentCursor()->unbind();
        $this->getCursor()->bind($this->root);
        return $this;
    }


    public function selectProperty(string $property)
    {
        if (!$this->isObjectSelected()) {
            throw new LogicException("Cursor must point an object to select property");
        }
        $parentData = &$this
            ->setCursorKey($property)
            ->getCursor()
            ->getDataReference();
        $this
            ->getParentCursor()
            ->bind($parentData);
        if (isset($parentData->{$property})) {
            $data = &$parentData->{$property};
            $this
                ->getCursor()
                ->bind($data);
        } else {
            $this
                ->getCursor()
                ->unbind();
            if ($this->isNumericProperty($property)) {
                // Numeric properties exported from array can be accessed only through iteration in PHP.
                foreach ($parentData as $existingProperty => &$value) {
                    if ($existingProperty == $property) {
                        $this
                            ->getCursor()
                            ->bind($value);
                        break;
                    }
                }
                unset($value);
            }
        }
        return $this;
    }


    public function isPropertySelected(): bool
    {
        return
            $this->getParentCursor()->isBound() &&
            is_object($this->getParentCursor()->getDataReference()) &&
            $this->isCursorKeySet();
    }


    public function isIndexSelected(): bool
    {
        return
            $this->getParentCursor()->isBound() &&
            is_array($this->getParentCursor()->getDataReference()) &&
            $this->isCursorKeySet();
    }


    public function isNewIndexSelected(): bool
    {
        return
            $this->getParentCursor()->isBound() &&
            is_array($this->getParentCursor()->getDataReference()) &&
            !$this->isCursorKeySet();
    }


    public function selectIndex(int $index)
    {
        if (!$this->isArraySelected()) {
            throw new LogicException("Cursor must point an array to select index");
        }
        $parentData = &$this
            ->setCursorKey($index)
            ->getCursor()
            ->getDataReference();
        $this->getParentCursor()->bind($parentData);
        if (array_key_exists($index, $parentData)) {
            $data = &$parentData[$index];
            $this->getCursor()->bind($data);
        } else {
            $this->getCursor()->unbind();
        }
        return $this;
    }


    public function selectNewIndex()
    {
        if (!$this->isArraySelected()) {
            throw new LogicException("Cursor must point an array to select index");
        }
        $this->unsetCursorKey();
        $parentData = &$this->getCursor()->getDataReference();
        $this->getParentCursor()->bind($parentData);
        $this->getCursor()->unbind();
        return $this;
    }


    public function isObjectSelected(): bool
    {
        return is_object($this->getCursor()->getDataReference());
    }


    public function isArraySelected(): bool
    {
        return is_array($this->getCursor()->getDataReference());
    }


    public function isStringSelected(): bool
    {
        return is_string($this->getCursor()->getDataReference());
    }


    public function isNumberSelected(): bool
    {
        $data = &$this->getCursor()->getDataReference();
        return is_int($data) || is_float($data);
    }


    public function isBooleanSelected(): bool
    {
        return is_bool($this->getCursor()->getDataReference());
    }


    public function isNullSelected(): bool
    {
        return is_null($this->getCursor()->getDataReference());
    }


    protected function isNumericProperty(string $key)
    {
        return preg_match('#^-?(?:0|[1-9]\d*)$#', $key) === 1;
    }


    protected function getCursor(): RawCursor
    {
        if (null === $this->cursor) {
            $this->cursor = new RawCursor();
        }
        return $this->cursor;
    }


    protected function getParentCursor(): RawCursor
    {
        if (null === $this->parentCursor) {
            $this->parentCursor = new RawCursor();
        }
        return $this->parentCursor;
    }


    protected function isCursorKeySet(): bool
    {
        return null !== $this->cursorKey;
    }


    /**
     * @return string|int
     */
    protected function getCursorKey()
    {
        if (!$this->isCursorKeySet()) {
            throw new LogicException("Cursor key is not set");
        }
        return $this->cursorKey;
    }


    protected function setCursorKey($key)
    {
        $this->cursorKey = $key;
        return $this;
    }


    protected function unsetCursorKey()
    {
        $this->cursorKey = null;
        return $this;
    }


    protected function getProperty(): string
    {
        if (!$this->isPropertySelected()) {
            throw new LogicException("Property is not selected");
        }
        return $this->getCursorKey();
    }


    protected function getIndex(): int
    {
        if (!$this->isIndexSelected()) {
            throw new LogicException("Index is not selected");
        }
        return $this->getCursorKey();
    }
}
