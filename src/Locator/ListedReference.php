<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

final class ListedReference implements ListedReferenceInterface
{
    public function __construct(
        private ReferenceInterface $reference,
        private bool $isLast,
    ) {
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
