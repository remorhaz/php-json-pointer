<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

interface LocatorInterface
{
    /**
     * @return array|ListedReferenceInterface[]
     */
    public function references(): array;
}
