<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query\Exception;

use LogicException;
use Throwable;

final class ParentNotFoundException extends LogicException implements ExceptionInterface
{

    private $source;

    public function __construct(string $source, Throwable $previous = null)
    {
        $this->source = $source;
        parent::__construct("Query '{$this->source}' selected no parent node", 0, $previous);
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
