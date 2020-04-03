<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\LocatorBuilderInterface;
use Remorhaz\JSON\Pointer\Parser\TranslationScheme;
use Remorhaz\JSON\Pointer\TokenMatcher;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Lexer\TokenReader;
use Remorhaz\UniLex\Parser\LL1\Parser as Ll1Parser;
use Remorhaz\UniLex\Parser\LL1\TranslationSchemeApplier;
use Remorhaz\UniLex\Parser\LL1\UnexpectedTokenException;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

use function count;

/**
 * @covers \Remorhaz\JSON\Pointer\Parser\TranslationScheme
 */
class TranslationSchemeTest extends TestCase
{

    /**
     * @param string $source
     * @param array  $expectedValues
     * @throws UniLexException
     * @dataProvider providerValidBuffer
     */
    public function testTranslation_ValidBuffer_BuildsMatchingLocator(string $source, array $expectedValues): void
    {
        $locatorBuilder = $this->createMock(LocatorBuilderInterface::class);
        $scheme = new TranslationScheme($locatorBuilder);
        $parser = $this->createParser($scheme, $source);

        $locatorBuilder
            ->expects(self::exactly(count($expectedValues)))
            ->method('addReference')
            ->withConsecutive(...$expectedValues);
        $parser->run();
    }

    /**
     * @param TranslationSchemeInterface $scheme
     * @param string                     $source
     * @return Ll1Parser
     * @throws UniLexException
     */
    private function createParser(TranslationSchemeInterface $scheme, string $source): Ll1Parser
    {
        $grammar = GrammarLoader::loadFile(__DIR__ . '/../../spec/GrammarSpec.php');
        $reader = new TokenReader(
            CharBufferFactory::createFromString($source),
            new TokenMatcher(),
            new TokenFactory($grammar)
        );
        $parser = new Ll1Parser(
            $grammar,
            $reader,
            new TranslationSchemeApplier($scheme)
        );
        $parser->loadLookupTable(__DIR__ . '/../../generated/LookupTable.php');

        return $parser;
    }

    public function providerValidBuffer(): array
    {
        return [
            'Empty string' => ['', []],
            'Empty property' => ['/', [['']]],
            'Single alpha property' => ['/a', [['a']]],
            'Single numeric property' => ['/1', [['1']]],
            'Single non-ASCII property' => ['/б', [['б']]],
            'Property sequence' => ['/a/1', [['a'], ['1']]],
            'Escaped property sequence' => ['/~0/~1', [['~'], ['/']]],
            'Escaped tilde in a word' => ['/a~0b', [['a~b']]],
            'Escaped tilde in a word before zero' => ['/a~00', [['a~0']]],
            'Escaped tilde in a word before one' => ['/a~01', [['a~1']]],
            'Escaped slash in a word' => ['/a~1b', [['a/b']]],
            'Escaped slash in a word before zero' => ['/a~10', [['a/0']]],
            'Escaped tilde then escaped slash' => ['/~0~1', [['~/']]],
            'Escaped slash then escaped tilde' => ['/~1~0', [['/~']]],
        ];
    }

    /**
     * @param string $source
     * @throws UniLexException
     * @dataProvider providerInvalidBuffer
     */
    public function testTranslation_InvalidBuffer_ThrowsException(string $source): void
    {
        $locatorBuilder = $this->createMock(LocatorBuilderInterface::class);
        $scheme = new TranslationScheme($locatorBuilder);
        $parser = $this->createParser($scheme, $source);
        $this->expectException(UnexpectedTokenException::class);
        $parser->run();
    }

    public function providerInvalidBuffer(): array
    {
        return [
            'No slash at start' => ['a'],
            'Double tilde' => ['/~~'],
            'Invalid number after tilde' => ['/~2'],
            'Letter after tilde' => ['/~a'],
            'Slash after tilde' => ['/~/'],
            'End of string after tilde' => ['/~'],
        ];
    }
}
