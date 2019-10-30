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
$context->setNewToken(TokenType::SLASH);

/**
 * @lexToken /~/
 */
$context->setNewToken(TokenType::TILDE);

/**
 * @lexToken /0/
 */
$context->setNewToken(TokenType::ZERO);

/**
 * @lexToken /1/
 */
$context->setNewToken(TokenType::ONE);

/**
 * @lexToken /[^/~01]+/
 */
$context
    ->setNewToken(TokenType::UNESCAPED)
    ->setTokenAttribute('text', $context->getSymbolString());
