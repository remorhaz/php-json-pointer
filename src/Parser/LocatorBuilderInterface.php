<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Parser;

use Remorhaz\JSON\Pointer\Locator\LocatorInterface;

interface LocatorBuilderInterface
{

    public function addReference(string $text): void;

    public function getLocator(): LocatorInterface;
}
