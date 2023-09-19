<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

interface LocatorBuilderInterface
{
    public function addReference(string $text): void;

    public function getLocator(): LocatorInterface;

    public function export(): string;
}
