<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query;

interface QueryFactoryInterface
{
    public function createQuery(string $source): QueryInterface;
}
