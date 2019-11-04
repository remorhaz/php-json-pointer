<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Locator\ReferenceInterface;

interface QueryResultInterface
{

    public function getSource(): string;

    public function getSelection(): NodeValueInterface;

    public function hasSelection(): bool;

    public function getParent(): NodeValueInterface;

    public function hasParent(): bool;

    public function getLastReference(): ReferenceInterface;

    public function hasLastReference(): bool;
}
