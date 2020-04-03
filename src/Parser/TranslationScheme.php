<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Parser;

use Remorhaz\JSON\Pointer\Locator\LocatorBuilderInterface;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;

final class TranslationScheme implements TranslationSchemeInterface
{

    private $locatorBuilder;

    public function __construct(LocatorBuilderInterface $locatorBuilder)
    {
        $this->locatorBuilder = $locatorBuilder;
    }

    public function applyTokenActions(Symbol $symbol, Token $token): void
    {
        $s = $symbol->getShortcut();
        $t = $token->getShortcut();
        switch ($symbol->getSymbolId()) {
            case SymbolType::T_UNESCAPED:
            case SymbolType::T_SLASH:
            case SymbolType::T_TILDE:
            case SymbolType::T_ZERO:
            case SymbolType::T_ONE:
                $s['s.text'] = $t['text'];
                break;
        }
    }

    public function applyProductionActions(Production $production): void
    {
        $header = $production->getHeaderShortcut();
        $symbols = $production->getSymbolListShortcut();
        $hash = "{$production->getHeader()->getSymbolId()}.{$production->getIndex()}";
        switch ($hash) {
            case SymbolType::NT_REFERENCE . '.0':
                // [ 0:NT_REFERENCE_PART, 1:NT_REFERENCE ]
            case SymbolType::NT_ESCAPED . '.0':
                // [ 0:T_TILDE, 1:NT_ESCAPED_SYMBOL ]
                $header['s.text'] = $symbols[1]['s.text'];
                break;

            case SymbolType::NT_REFERENCE . '.1':
                // [ ]
                $header['s.text'] = $header['i.text'];
                break;

            case SymbolType::NT_REFERENCE_PART . '.0':
                // [ 0:NT_UNESCAPED ]
            case SymbolType::NT_REFERENCE_PART . '.1':
                // [ 0:NT_ESCAPED ]
            case SymbolType::NT_UNESCAPED . '.0':
                // [ 0:T_UNESCAPED ]
            case SymbolType::NT_UNESCAPED . '.1':
                // [ 0:T_ZERO ]
            case SymbolType::NT_UNESCAPED . '.2':
                // [ 0:T_ONE ]
                $header['s.text'] = $symbols[0]['s.text'];
                break;

            case SymbolType::NT_ESCAPED_SYMBOL . '.0':
                // [ 0:T_ZERO ]
                $header['s.text'] = '~';
                break;

            case SymbolType::NT_ESCAPED_SYMBOL . '.1':
                // [ 0:T_ONE ]
                $header['s.text'] = '/';
                break;
        }
    }

    public function applySymbolActions(Production $production, int $symbolIndex): void
    {
        $header = $production->getHeaderShortcut();
        $symbols = $production->getSymbolListShortcut();
        $hash = "{$production->getHeader()->getSymbolId()}.{$production->getIndex()}.{$symbolIndex}";
        switch ($hash) {
            case SymbolType::NT_REFERENCE_LIST . '.0.1':
                // [ 0:T_SLASH, 1:NT_REFERENCE, 2:NT_REFERENCE_LIST ]
                $symbols[1]['i.text'] = '';
                break;

            case SymbolType::NT_REFERENCE_LIST . '.0.2':
                // [ 0:T_SLASH, 1:NT_REFERENCE, 2:NT_REFERENCE_LIST ]
                $this
                    ->locatorBuilder
                    ->addReference($symbols[1]['s.text']);
                break;

            case SymbolType::NT_REFERENCE . '.0.1':
                // [ 0:NT_REFERENCE_PART, 1:NT_REFERENCE ]
                $symbols[1]['i.text'] = $header['i.text'] . $symbols[0]['s.text'];
                break;
        }
    }
}
