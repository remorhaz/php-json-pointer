<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

use function count;

final class Locator implements LocatorInterface
{
    private $listedReferences;

    public function __construct(ReferenceInterface ...$references)
    {
        $listSize = count($references);
        $this->listedReferences = [];
        foreach ($references as $index => $reference) {
            $this->listedReferences[] = new ListedReference($reference, $index + 1 == $listSize);
        }
    }

    /**
     * @return array|ListedReferenceInterface[]
     */
    public function references(): array
    {
        return $this->listedReferences;
    }
}
