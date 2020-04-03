<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

final class IndexReference implements IndexReferenceInterface
{

    private $elementIndex;

    public function __construct(int $elementIndex)
    {
        $this->elementIndex = $elementIndex;
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
