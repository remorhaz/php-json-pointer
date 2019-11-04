<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

final class ListedReference implements ListedReferenceInterface
{

    private $reference;

    private $isLast;

    public function __construct(ReferenceInterface $reference, bool $isLast)
    {
        $this->reference = $reference;
        $this->isLast = $isLast;
    }

    public function getReference(): ReferenceInterface
    {
        return $this->reference;
    }

    public function isLast(): bool
    {
        return $this->isLast;
    }
}
