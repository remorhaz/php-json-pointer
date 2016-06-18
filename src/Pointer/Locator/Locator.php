<?php

namespace Remorhaz\JSONPointer\Pointer\Locator;

class Locator
{

    /**
     * Reference list.
     *
     * @var Reference[]
     */
    private $referenceList = [];

    /**
     * @var Reference|null
     */
    private $lastReference;


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
        $this->referenceList[] = $reference;
        $pathPrefix = null === $this->lastReference
            ? ''
            : $this
                ->lastReference
                ->markAsNotLast()
                ->getPath();

        $this->lastReference = $reference
            ->markAsLast()
            ->setPath("{$pathPrefix}/{$reference->getText()}");
        return $this;
    }
}
