<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Parser;

use Remorhaz\UniLex\Parser\LL1\Parser as Ll1Parser;

interface Ll1ParserFactoryInterface
{

    public function createParser(string $pointer, LocatorBuilderInterface $locatorBuilder): Ll1Parser;
}
