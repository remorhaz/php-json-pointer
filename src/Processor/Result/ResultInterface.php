<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Result;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

interface ResultInterface
{
    public function exists(): bool;

    public function encode(): string;

    public function decode(): mixed;

    public function get(): NodeValueInterface;
}
