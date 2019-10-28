<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

interface LocatorInterface
{

    /**
     * Returns reference list.
     *
     * @return ReferenceInterface[]
     */
    public function getReferenceList(): array;

    public function pointsNewElement(): bool;
}
