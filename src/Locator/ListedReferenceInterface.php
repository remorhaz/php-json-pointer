<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

interface ListedReferenceInterface
{

    public function getReference(): ReferenceInterface;

    public function isLast(): bool;
}
