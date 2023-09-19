<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

interface IndexReferenceInterface extends ReferenceInterface
{
    public function getElementIndex(): int;
}
