<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

interface ReferenceFactoryInterface
{
    public function createReference(string $text): ReferenceInterface;
}
