<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Parser\Exception;

use LogicException;
use Throwable;

final class LL1ParserNotCreatedException extends LogicException implements ExceptionInterface
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct("Failed to create LL(1) parser", 0, $previous);
    }
}
