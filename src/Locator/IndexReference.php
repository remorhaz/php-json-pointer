<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

final class IndexReference implements IndexReferenceInterface
{
    public function __construct(
        private int $elementIndex,
    ) {
    }

    public function getElementIndex(): int
    {
        return $this->elementIndex;
    }

    public function getPropertyName(): string
    {
        return (string) $this->elementIndex;
    }
}
