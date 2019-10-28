<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Result;

final class NonExistingResult implements ResultInterface
{

    private $source;

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public function exists(): bool
    {
        return false;
    }

    public function encode(): string
    {
        throw new Exception\ResultNotFoundException($this->source);
    }

    public function decode()
    {
        throw new Exception\ResultNotFoundException($this->source);
    }
}
