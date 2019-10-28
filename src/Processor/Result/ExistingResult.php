<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Result;

use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

final class ExistingResult implements ResultInterface
{

    private $encoder;

    private $decoder;

    private $nodeValue;

    public function __construct(
        ValueEncoderInterface $encoder,
        ValueDecoderInterface $decoder,
        NodeValueInterface $nodeValue
    ) {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->nodeValue = $nodeValue;
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

    public function decode()
    {
        return $this
            ->decoder
            ->exportValue($this->nodeValue);
    }
}
