<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Query;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Locator\LocatorInterface;
use Remorhaz\JSON\Pointer\Parser\ParserInterface;
use Remorhaz\JSON\Pointer\Query\LazyQuery;
use Remorhaz\JSON\Pointer\Query\QueryFactory;

/**
 * @covers \Remorhaz\JSON\Pointer\Query\QueryFactory
 */
class QueryFactoryTest extends TestCase
{

    public function testCreate_Always_ReturnsQueryFactoryInstance(): void
    {
        self::assertInstanceOf(QueryFactory::class, QueryFactory::create());
    }

    public function testCreateQuery_Constructed_ReturnsLazyQueryInstance(): void
    {
        $factory = QueryFactory::create();
        self::assertInstanceOf(LazyQuery::class, $factory->createQuery(''));
    }

    public function testCreateQuery_ConstructedWithParser_ResultCallsSameParserInstanceOnExecution(): void
    {
        $parser = $this->createMock(ParserInterface::class);
        $factory = new QueryFactory($parser);
        $query = $factory->createQuery('a');
        $nodeValue = $this->createMock(NodeValueInterface::class);

        $parser
            ->expects(self::once())
            ->method('buildLocator')
            ->with('a');
        $query($nodeValue);
    }

    public function testCreateQuery_ConstructedWithParser_ResultUsesLocatorCreatedByParserOnExecution(): void
    {
        $parser = $this->createMock(ParserInterface::class);
        $factory = new QueryFactory($parser);
        $query = $factory->createQuery('a');
        $nodeValue = $this->createMock(NodeValueInterface::class);

        $locator = $this->createMock(LocatorInterface::class);
        $parser
            ->method('buildLocator')
            ->willReturn($locator);
        $locator
            ->expects(self::once())
            ->method('references');
        $query($nodeValue);
    }
}
