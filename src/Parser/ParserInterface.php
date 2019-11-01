<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Parser;

use Remorhaz\JSON\Pointer\Locator\LocatorRefInterface;

interface ParserInterface
{

    public function buildLocator(string $pointer): LocatorRefInterface;
}
