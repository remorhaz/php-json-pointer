<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

use function array_values;
use function count;

final class Locator implements LocatorInterface
{
    /**
     * @var list<ListedReferenceInterface>
     */
    private array $listedReferences;

    public function __construct(ReferenceInterface ...$references)
    {
        $listSize = count($references);
        $this->listedReferences = [];
        foreach (array_values($references) as $index => $reference) {
            $this->listedReferences[] = new ListedReference($reference, $index + 1 == $listSize);
        }
    }

    /**
     * @return list<ListedReferenceInterface>
     */
    public function references(): array
    {
        return $this->listedReferences;
    }
}
