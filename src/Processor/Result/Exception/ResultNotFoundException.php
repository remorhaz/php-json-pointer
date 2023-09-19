<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Result\Exception;

use LogicException;
use Throwable;

final class ResultNotFoundException extends LogicException implements ExceptionInterface
{
    public function __construct(
        private string $source,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Result not found for query '$this->source'", 0, $previous);
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
