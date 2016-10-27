<?php

namespace Remorhaz\JSONPointer\Data;

interface SelectableAccessorInterface extends AccessorInterface {


    public function selectRoot();


    public function selectProperty(string $key);


    public function selectIndex(int $index);


    public function isArraySelected(): bool;


    public function isObjectSelected(): bool;


    public function isStringSelected(): bool;


    public function isNumberSelected(): bool;


    public function isBooleanSelected(): bool;


    public function isNullSelected(): bool;
}
