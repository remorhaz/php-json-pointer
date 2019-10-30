<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Parser;

use Remorhaz\JSON\Pointer\Locator\Locator;
use Remorhaz\UniLex\Parser\LL1\Parser as Ll1Parser;

interface Ll1ParserFactoryInterface
{

    public function createParser(string $pointer, Locator $locator): Ll1Parser;
}
