<?php

namespace Remorhaz\JSON\Pointer\Locator;

class Locator
{

    /**
     * Reference list.
     *
     * @var Reference[]
     */
    private $referenceList = [];


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


    public function getText(): string
    {
        if (empty($this->referenceList)) {
            return "";
        }
        $text = "";
        foreach ($this->referenceList as $reference) {
            $text .= "/{$reference->getText()}";
        }
        return $text;
    }


    /**
     * Returns reference list.
     *
     * @return Reference[]
     */
    public function getReferenceList(): array
    {
        return $this->referenceList;
    }


    /**
     * Adds reference to list.
     *
     * @param Reference $reference
     * @return $this
     */
    public function addReference(Reference $reference)
    {
        $index = count($this->referenceList);
        $this->referenceList[$index] = $reference
            ->setLocator($this)
            ->setIndex($index);
        return $this;
    }


    /**
     * @param int $index
     * @return bool
     */
    public function hasReference(int $index): bool
    {
        return isset($this->referenceList[$index]);
    }


    /**
     * @param int $index
     * @return Reference
     */
    public function getReference(int $index): Reference
    {
        if (!$this->hasReference($index)) {
            throw new OutOfBoundsException("Invalid reference index {$index}");
        }
        return $this->referenceList[$index];
    }
}
