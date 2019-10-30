<?php
/**
 * JSON Pointer parser LL(1) lookup table.
 *
 * Auto-generated file, please don't edit manually.
 * Run following command to update this file:
 *     vendor/bin/phing json-pointer-lookup
 *
 * Phing version: 2.16.1
 */

use Remorhaz\JSON\Pointer\Parser\SymbolType;
use Remorhaz\JSON\Pointer\Parser\TokenType;

return [
    SymbolType::NT_POINTER => [
        TokenType::SLASH => 0,
        TokenType::EOI => 0,
    ],
    SymbolType::NT_REFERENCE_LIST => [
        TokenType::SLASH => 0,
        TokenType::EOI => 1,
    ],
    SymbolType::NT_REFERENCE => [
        TokenType::UNESCAPED => 0,
        TokenType::ONE => 0,
        TokenType::ZERO => 0,
        TokenType::TILDE => 0,
        TokenType::SLASH => 1,
        TokenType::EOI => 1,
    ],
    SymbolType::NT_REFERENCE_PART => [
        TokenType::UNESCAPED => 0,
        TokenType::ONE => 0,
        TokenType::ZERO => 0,
        TokenType::TILDE => 1,
    ],
    SymbolType::NT_UNESCAPED => [
        TokenType::UNESCAPED => 0,
        TokenType::ONE => 1,
        TokenType::ZERO => 2,
    ],
    SymbolType::NT_ESCAPED => [
        TokenType::TILDE => 0,
    ],
    SymbolType::NT_ESCAPED_SYMBOL => [
        TokenType::ZERO => 0,
        TokenType::ONE => 1,
    ],
];
