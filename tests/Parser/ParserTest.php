<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\ReferenceFactoryInterface;
use Remorhaz\JSON\Pointer\Locator\ReferenceInterface;
use Remorhaz\JSON\Pointer\Parser\Ll1ParserFactory;
use Remorhaz\JSON\Pointer\Parser\Ll1ParserFactoryInterface;
use Remorhaz\JSON\Pointer\Parser\Parser;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Parser\LL1\Parser as LL1Parser;

/**
 * @covers \Remorhaz\JSON\Pointer\Parser\Parser
 */
class ParserTest extends TestCase
{

    public function testCreate_Always_CreatesParserInstance(): void
    {
        self::assertInstanceOf(Parser::class, Parser::create());
    }

    /**
     * @throws UniLexException
     */
    public function testBuildLocator_ConstructedWithLL1ParserFactory_PassesPointerToSameInstance(): void
    {
        $ll1ParserFactory = $this->createMock(Ll1ParserFactoryInterface::class);
        $parser = new Parser(
            $ll1ParserFactory,
            $this->createMock(ReferenceFactoryInterface::class)
        );

        $ll1ParserFactory
            ->expects(self::once())
            ->method('createParser')
            ->with('a', self::anything());
        $parser->buildLocator('a');
    }

    /**
     * @throws UniLexException
     */
    public function testBuildLocator_ConstructedWithLL1ParserFactory_RunsCreatedParserInstance(): void
    {
        $ll1ParserFactory = $this->createMock(Ll1ParserFactoryInterface::class);
        $parser = new Parser(
            $ll1ParserFactory,
            $this->createMock(ReferenceFactoryInterface::class)
        );

        $ll1Parser = $this->createMock(LL1Parser::class);
        $ll1ParserFactory
            ->method('createParser')
            ->willReturn($ll1Parser);
        $ll1Parser
            ->expects(self::once())
            ->method('run');
        $parser->buildLocator('a');
    }

    /**
     * @throws UniLexException
     */
    public function testBuildLocator_ConstructedWithReferenceFactory_UsesSameInstance(): void
    {
        $referenceFactory = $this->createMock(ReferenceFactoryInterface::class);
        $parser = new Parser(new Ll1ParserFactory, $referenceFactory);
        $referenceFactory
            ->expects(self::exactly(2))
            ->method('createReference')
            ->withConsecutive(
                ['a'],
                ['1']
            );
        $parser->buildLocator('/a/1');
    }

    /**
     * @throws UniLexException
     */
    public function testBuildLocator_ReferenceFactoryReturnsValue_ResultHasSameValue(): void
    {
        $referenceFactory = $this->createMock(ReferenceFactoryInterface::class);
        $parser = new Parser(new Ll1ParserFactory, $referenceFactory);
        $reference = $this->createMock(ReferenceInterface::class);
        $referenceFactory
            ->method('createReference')
            ->willReturn($reference);
        $locator = $parser->buildLocator('/a');
        $references = $locator->references();
        self::assertSame($reference, isset($references[0]) ? $references[0]->getReference() : null);
    }
}
