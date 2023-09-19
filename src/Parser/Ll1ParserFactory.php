<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Parser;

use Remorhaz\JSON\Pointer\Locator\LocatorBuilderInterface;
use Remorhaz\JSON\Pointer\TokenMatcher;
use Remorhaz\UniLex\Exception as UnilexException;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\Lexer\TokenReader;
use Remorhaz\UniLex\Lexer\TokenReaderInterface;
use Remorhaz\UniLex\Parser\LL1\Parser as Ll1Parser;
use Remorhaz\UniLex\Parser\LL1\TranslationSchemeApplier;
use Remorhaz\UniLex\Unicode\CharBufferFactory;
use Throwable;

final class Ll1ParserFactory implements Ll1ParserFactoryInterface
{
    private ?GrammarInterface $grammar = null;

    public function createParser(string $pointer, LocatorBuilderInterface $locatorBuilder): Ll1Parser
    {
        try {
            $scheme = new TranslationScheme($locatorBuilder);
            $parser = new Ll1Parser(
                $this->getGrammar(),
                $this->createSourceReader($pointer),
                new TranslationSchemeApplier($scheme),
            );
            $parser->loadLookupTable(__DIR__ . '/../../generated/LookupTable.php');
        } catch (Throwable $e) {
            throw new Exception\LL1ParserNotCreatedException($e);
        }

        return $parser;
    }

    /**
     * @throws UnilexException
     */
    private function getGrammar(): GrammarInterface
    {
        return $this->grammar ??= GrammarLoader::loadFile(__DIR__ . '/../../spec/GrammarSpec.php');
    }

    /**
     * @throws UnilexException
     */
    private function createSourceReader(string $source): TokenReaderInterface
    {
        return new TokenReader(
            CharBufferFactory::createFromString($source),
            new TokenMatcher(),
            new TokenFactory($this->getGrammar()),
        );
    }
}
