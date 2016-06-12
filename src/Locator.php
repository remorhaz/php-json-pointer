<?php

namespace Remorhaz\JSONPointer;

class Locator
{

    /**
     * Reference list.
     *
     * @var Locator\Reference[]
     */
    protected $referenceList = [];

    /**
     * @var Locator\Reference|null
     */
    protected $lastReference;


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
     * @return Locator\Reference[]
     */
    public function getReferenceList()
    {
        return $this->referenceList;
    }


    /**
     * Adds reference to list.
     *
     * @param Locator\Reference $reference
     * @return $this
     */
    public function addReference(Locator\Reference $reference)
    {
        $this->referenceList[] = $reference;
        $pathPrefix = null === $this->lastReference
            ? ''
            : $this
                ->lastReference
                ->setIsLast(false)
                ->getPath();

        $this->lastReference = $reference
            ->setIsLast()
            ->setPath("{$pathPrefix}/{$reference->getText()}");
        return $this;
    }
}
