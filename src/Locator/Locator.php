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
    private readonly array $listedReferences;

    public function __construct(ReferenceInterface ...$references)
    {
        $this->listedReferences = $this->buildListedReferences(...$references);
    }


    private function buildListedReferences(ReferenceInterface ...$references): array
    {
        $listSize = count($references);
        $listedReferences = [];
        foreach (array_values($references) as $index => $reference) {
            $listedReferences[] = new ListedReference($reference, $index + 1 == $listSize);
        }

        return $listedReferences;
    }

    /**
     * @return list<ListedReferenceInterface>
     */
    public function references(): array
    {
        return $this->listedReferences;
    }
}
