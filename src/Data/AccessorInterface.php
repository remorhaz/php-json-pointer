<?php

namespace Remorhaz\JSONPointer\Data;

interface AccessorInterface {


    public function getDataFormat(): string;


    public function getData();


    public function setData(self $source);


    public function hasData(): bool;


    public function unsetData();
}
