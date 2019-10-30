<?php
/**
 * JSON Pointer token matcher.
 *
 * Auto-generated file, please don't edit manually.
 * Run following command to update this file:
 *     vendor/bin/phing json-path-matcher
 *
 * Phing version: 2.16.1
 */

namespace Remorhaz\JSON\Pointer;

use Remorhaz\JSON\Pointer\Parser\TokenType;
use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\Lexer\TokenFactoryInterface;
use Remorhaz\UniLex\Lexer\TokenMatcherTemplate;

class TokenMatcher extends TokenMatcherTemplate
{

    public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool
    {
        $context = $this->createContext($buffer, $tokenFactory);
        goto state1;

        state1:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x2F == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::SLASH);
            return true;
        }
        if (0x7E == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::TILDE);
            return true;
        }
        if (0x30 == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::ZERO);
            return true;
        }
        if (0x31 == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::ONE);
            return true;
        }
        if (0x00 <= $char && $char <= 0x2E || 0x32 <= $char && $char <= 0x7D || 0x7F <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            goto state6;
        }
        goto error;

        state6:
        if ($context->getBuffer()->isEnd()) {
            goto finish6;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x00 <= $char && $char <= 0x2E || 0x32 <= $char && $char <= 0x7D || 0x7F <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            goto state7;
        }
        finish6:
        $context->setNewToken(TokenType::UNESCAPED);
        return true;

        state7:
        if ($context->getBuffer()->isEnd()) {
            goto finish7;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x00 <= $char && $char <= 0x2E || 0x32 <= $char && $char <= 0x7D || 0x7F <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            goto state7;
        }
        finish7:
        $context->setNewToken(TokenType::UNESCAPED);
        return true;

        error:
        return false;
    }
}
