<?php

namespace Remorhaz\JSONPointer\Data;

interface SelectableInterface extends ReaderInterface
{


    /**
     * @return $this
     */
    public function selectRoot();


    /**
     * @param string $property
     * @return $this
     */
    public function selectProperty(string $property);


    /**
     * @param int $index
     * @return $this
     */
    public function selectIndex(int $index);


    /**
     * @return $this
     */
    public function selectNewIndex();


    public function isArraySelected(): bool;


    public function isObjectSelected(): bool;


    public function isStringSelected(): bool;


    public function isNumberSelected(): bool;


    public function isBooleanSelected(): bool;


    public function isNullSelected(): bool;


    public function isPropertySelected(): bool;


    public function isIndexSelected(): bool;


    public function isNewIndexSelected(): bool;
}
