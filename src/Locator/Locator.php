<?php

namespace Remorhaz\JSON\Pointer\Locator;

class Locator implements LocatorInterface
{

    /**
     * Reference list.
     *
     * @var Reference[]
     */
    private $referenceList = [];

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
     * @return ReferenceInterface[]
     */
    public function getReferenceList(): array
    {
        return $this->referenceList;
    }

    public function pointsNewElement(): bool
    {
        $isNewElement = false;
        foreach ($this->referenceList as $reference) {
            $isNewElement = $reference->getType() == $reference::TYPE_NEXT_INDEX;
        }

        return $isNewElement;
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
