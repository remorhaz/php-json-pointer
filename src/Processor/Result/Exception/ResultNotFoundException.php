<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Result\Exception;

use LogicException;
use Throwable;

final class ResultNotFoundException extends LogicException implements ExceptionInterface
{

    private $source;

    public function __construct(string $source, Throwable $previous = null)
    {
        $this->source = $source;
        parent::__construct("Result not found for query '{$this->source}'", 0, $previous);
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
