<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Parser;

interface LocatorBuilderInterface
{

    public function addReference(string $text): void;
}
