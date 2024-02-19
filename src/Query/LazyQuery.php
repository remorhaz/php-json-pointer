<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Parser\ParserInterface;

final class LazyQuery implements QueryInterface
{
    private ?QueryInterface $loadedQuery = null;

    public function __construct(
        private readonly string $source,
        private readonly ?ParserInterface $parser,
    ) {
    }

    public function __invoke(NodeValueInterface $rootNode): QueryResultInterface
    {
        return $this->getLoadedQuery()($rootNode);
    }

    public function getSource(): string
    {
        return $this->source;
    }

    private function getLoadedQuery(): QueryInterface
    {
        return $this->loadedQuery ??= $this->loadQuery();
    }

    private function loadQuery(): QueryInterface
    {
        return new Query(
            $this->source,
            $this
                ->parser
                ->buildLocator($this->source),
        );
    }
}
