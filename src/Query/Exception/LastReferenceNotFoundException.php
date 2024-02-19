<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query\Exception;

use LogicException;
use Throwable;

final class LastReferenceNotFoundException extends LogicException implements ExceptionInterface
{
    public function __construct(
        private readonly string $source,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Query '$this->source' selected no last reference", previous: $previous);
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
