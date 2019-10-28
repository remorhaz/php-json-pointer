<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Parser;

use Remorhaz\JSON\Pointer\Locator\LocatorInterface;

interface ParserInterface
{

    public function setText(string $text): self;

    public function getLocator(): LocatorInterface;
}
