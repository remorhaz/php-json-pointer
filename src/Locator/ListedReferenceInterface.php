<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

interface ListedReferenceInterface
{

    public function getReference(): ReferenceRefInterface;

    public function isLast(): bool;
}
