<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Locator\ReferenceInterface;

final class QueryResult implements QueryResultInterface
{

    private $selection;

    private $parent;

    private $lastReference;

    public function __construct(
        ?NodeValueInterface $selection = null,
        ?NodeValueInterface $parent = null,
        ReferenceInterface $lastReference = null
    ) {
        $this->selection = $selection;
        $this->parent = $parent;
        $this->lastReference = $lastReference;
    }

    public function getSelection(): NodeValueInterface
    {
        if (isset($this->selection)) {
            return $this->selection;
        }

        throw new Exception\SelectonNotFoundException;
    }

    public function hasSelection(): bool
    {
        return isset($this->selection);
    }

    public function getParent(): NodeValueInterface
    {
        if (isset($this->parent)) {
            return $this->parent;
        }

        throw new Exception\ParentNotFoundException;
    }

    public function hasParent(): bool
    {
        return isset($this->parent);
    }

    public function getLastReference(): ReferenceInterface
    {
        if (isset($this->lastReference)) {
            return $this->lastReference;
        }

        throw new Exception\LastReferenceNotFound;
    }

    public function hasLastReference(): bool
    {
        return isset($this->lastReference);
    }
}
