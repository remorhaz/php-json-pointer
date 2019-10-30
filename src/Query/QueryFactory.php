<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Query;

use Remorhaz\JSON\Pointer\Parser\ParserInterface;
use Remorhaz\JSON\Pointer\Parser\Parser;

final class QueryFactory implements QueryFactoryInterface
{

    private $parser;

    public static function create(): QueryFactoryInterface
    {
        return new self(Parser::create());
    }

    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    public function createQuery(string $source): QueryInterface
    {
        return new LazyQuery($source, $this->parser);
    }
}
