<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

use function intval;
use function preg_match;

final class ReferenceFactory implements ReferenceFactoryInterface
{

    public function createReference(string $text): ReferenceInterface
    {
        if ('-' == $text) {
            return new NextIndexReference;
        }

        $isIndex = 1 === preg_match('/^(?:0|[1-9][0-9]*)$/', $text);

        return $isIndex
            ? new IndexReference(intval($text))
            : new PropertyReference($text);
    }
}
