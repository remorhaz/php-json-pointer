<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

interface LocatorRefInterface
{

    /**
     * @return array|ListedReferenceInterface[]
     */
    public function references(): array;
}
