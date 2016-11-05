<?php

namespace Remorhaz\JSONPointer\Data;

interface ReaderInterface {


    public function getData();


    public function hasData(): bool;
}
