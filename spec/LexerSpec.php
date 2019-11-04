<?php
/**
 * @var \Remorhaz\UniLex\Lexer\TokenMatcherContextInterface $context
 * @lexHeader
 * @lexTargetClass Remorhaz\JSON\Pointer\TokenMatcher
 */

use Remorhaz\JSON\Pointer\Parser\TokenType;

/**
 * @lexToken ///
 */
$context
    ->setNewToken(TokenType::SLASH)
    ->setTokenAttribute('text', '/');

/**
 * @lexToken /~/
 */
$context
    ->setNewToken(TokenType::TILDE)
    ->setTokenAttribute('text', '~');

/**
 * @lexToken /0/
 */
$context
    ->setNewToken(TokenType::ZERO)
    ->setTokenAttribute('text', '0');

/**
 * @lexToken /1/
 */
$context
    ->setNewToken(TokenType::ONE)
    ->setTokenAttribute('text', '1');

/**
 * @lexToken /[^/~01]+/
 */
$context
    ->setNewToken(TokenType::UNESCAPED)
    ->setTokenAttribute('text', $context->getSymbolString());
