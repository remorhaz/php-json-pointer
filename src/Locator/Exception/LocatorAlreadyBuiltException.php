<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator\Exception;

use LogicException;
use Throwable;

final class LocatorAlreadyBuiltException extends LogicException implements ExceptionInterface
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct("Locator is already built", 0, $previous);
    }
}
