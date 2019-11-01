<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

final class NextIndexReference implements NextIndexReferenceInterface
{

    public function getPropertyName(): string
    {
        return '-';
    }
}
