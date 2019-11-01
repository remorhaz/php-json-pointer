<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Locator\ReferenceRefInterface;

final class QueryResult implements QueryResultInterface
{

    private $source;

    private $selection;

    private $parent;

    private $lastReference;

    public function __construct(
        string $source,
        ?NodeValueInterface $selection = null,
        ?NodeValueInterface $parent = null,
        ReferenceRefInterface $lastReference = null
    ) {
        $this->source = $source;
        $this->selection = $selection;
        $this->parent = $parent;
        $this->lastReference = $lastReference;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getSelection(): NodeValueInterface
    {
        if (isset($this->selection)) {
            return $this->selection;
        }

        throw new Exception\SelectionNotFoundException($this->source);
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

        throw new Exception\ParentNotFoundException($this->source);
    }

    public function hasParent(): bool
    {
        return isset($this->parent);
    }

    public function getLastReference(): ReferenceRefInterface
    {
        if (isset($this->lastReference)) {
            return $this->lastReference;
        }

        throw new Exception\LastReferenceNotFoundException($this->source);
    }

    public function hasLastReference(): bool
    {
        return isset($this->lastReference);
    }
}
