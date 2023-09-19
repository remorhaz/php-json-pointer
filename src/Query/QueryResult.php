<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Locator\ReferenceInterface;

final class QueryResult implements QueryResultInterface
{
    public function __construct(
        private string $source,
        private ?NodeValueInterface $selection = null,
        private ?NodeValueInterface $parent = null,
        private ?ReferenceInterface $lastReference = null,
    ) {
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getSelection(): NodeValueInterface
    {
        return $this->selection ?? throw new Exception\SelectionNotFoundException($this->source);
    }

    public function hasSelection(): bool
    {
        return isset($this->selection);
    }

    public function getParent(): NodeValueInterface
    {
        return $this->parent ?? throw new Exception\ParentNotFoundException($this->source);
    }

    public function hasParent(): bool
    {
        return isset($this->parent);
    }

    public function getLastReference(): ReferenceInterface
    {
        return $this->lastReference ?? throw new Exception\LastReferenceNotFoundException($this->source);
    }

    public function hasLastReference(): bool
    {
        return isset($this->lastReference);
    }
}
