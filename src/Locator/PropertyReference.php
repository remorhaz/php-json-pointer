<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

final class PropertyReference implements ReferenceInterface
{
    public function __construct(
        private readonly string $propertyName,
    ) {
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }
}
