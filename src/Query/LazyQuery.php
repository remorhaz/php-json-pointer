<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Parser\ParserInterface;

final class LazyQuery implements QueryInterface
{

    private $source;

    private $parser;

    private $loadedQuery;

    public function __construct(string $source, ParserInterface $parser)
    {
        $this->source = $source;
        $this->parser = $parser;
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
        if (!isset($this->loadedQuery)) {
            $this->loadedQuery = $this->loadQuery();
        }

        return $this->loadedQuery;
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
