<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

interface QueryInterface
{

    public function __invoke(NodeValueInterface $rootNode): QueryResultInterface;

    public function getSource(): string;

    public function getCapabilities(): QueryCapabilitiesInterface;
}
