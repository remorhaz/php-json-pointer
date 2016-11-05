<?php

namespace Remorhaz\JSONPointer\Data;

interface SelectorInterface {


    public function selectRoot();


    public function selectProperty(string $property);


    public function selectIndex(int $index);


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
