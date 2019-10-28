<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Result;

interface ResultInterface
{

    public function exists(): bool;

    public function encode(): string;

    public function decode();
}
