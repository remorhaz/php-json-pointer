<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Processor\Exception;

use InvalidArgumentException;
use Remorhaz\JSON\Pointer\Query\QueryInterface;
use Throwable;

final class QueryNotSelectableException extends InvalidArgumentException implements ExceptionInterface
{

    private $query;

    public function __construct(QueryInterface $query, Throwable $previous = null)
    {
        $this->query = $query;
        parent::__construct("Query is not selectable", 0, $previous);
    }

    public function getQuery(): QueryInterface
    {
        return $this->query;
    }
}
