<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Result;

use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

final class ExistingResult implements ResultInterface
{
    public function __construct(
        private readonly ValueEncoderInterface $encoder,
        private readonly ValueDecoderInterface $decoder,
        private readonly NodeValueInterface $nodeValue,
    ) {
    }

    public function exists(): bool
    {
        return true;
    }

    public function encode(): string
    {
        return $this
            ->encoder
            ->exportValue($this->nodeValue);
    }

    public function decode(): mixed
    {
        return $this
            ->decoder
            ->exportValue($this->nodeValue);
    }

    public function get(): NodeValueInterface
    {
        return $this->nodeValue;
    }
}
