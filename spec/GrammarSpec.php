<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer;

use Remorhaz\JSON\Pointer\Parser\SymbolType;
use Remorhaz\JSON\Pointer\Parser\TokenType;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;

return [
    GrammarLoader::ROOT_SYMBOL_KEY => SymbolType::NT_ROOT,
    GrammarLoader::EOI_SYMBOL_KEY => SymbolType::T_EOI,
    GrammarLoader::START_SYMBOL_KEY => SymbolType::NT_POINTER,

    GrammarLoader::TOKEN_MAP_KEY => [
        SymbolType::T_SLASH => TokenType::SLASH,
        SymbolType::T_TILDE => TokenType::TILDE,
        SymbolType::T_ZERO => TokenType::ZERO,
        SymbolType::T_ONE => TokenType::ONE,
        SymbolType::T_UNESCAPED => TokenType::UNESCAPED,
    ],

    GrammarLoader::PRODUCTION_MAP_KEY => [
        SymbolType::NT_ROOT => [
            0 => [SymbolType::NT_POINTER, SymbolType::T_EOI],
        ],
        SymbolType::NT_POINTER => [
            0 => [SymbolType::NT_REFERENCE_LIST],
        ],
        SymbolType::NT_REFERENCE_LIST => [
            0 => [SymbolType::T_SLASH, SymbolType::NT_REFERENCE, SymbolType::NT_REFERENCE_LIST],
            1 => [],
        ],
        SymbolType::NT_REFERENCE => [
            0 => [SymbolType::NT_REFERENCE_PART, SymbolType::NT_REFERENCE],
            1 => [],
        ],
        SymbolType::NT_REFERENCE_PART => [
            0 => [SymbolType::NT_UNESCAPED],
            1 => [SymbolType::NT_ESCAPED],
        ],
        SymbolType::NT_UNESCAPED => [
            0 => [SymbolType::T_UNESCAPED],
            1 => [SymbolType::T_ONE],
            2 => [SymbolType::T_ZERO],
        ],
        SymbolType::NT_ESCAPED => [
            0 => [SymbolType::T_TILDE, SymbolType::NT_ESCAPED_SYMBOL],
        ],
        SymbolType::NT_ESCAPED_SYMBOL => [
            0 => [SymbolType::T_ZERO],
            1 => [SymbolType::T_ONE],
        ],
    ],
];
