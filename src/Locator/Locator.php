<?php

namespace Remorhaz\JSONPointer\Locator;

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


    /**
     * Returns reference list.
     *
     * @return Reference[]
     */
    public function getReferenceList()
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
    public function hasReference($index)
    {
        return isset($this->referenceList[(int) $index]);
    }


    /**
     * @param int $index
     * @return Reference
     */
    public function getReference($index)
    {
        if (!$this->hasReference($index)) {
            throw new OutOfBoundsException("Invalid reference index {$index}");
        }
        return $this->referenceList[(int) $index];
    }
}
