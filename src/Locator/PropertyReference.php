<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

final class PropertyReference implements ReferenceInterface
{

    private $propertyName;

    public function __construct(string $propertyName)
    {
        $this->propertyName = $propertyName;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }
}
