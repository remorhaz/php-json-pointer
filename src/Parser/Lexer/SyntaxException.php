<?php

namespace Remorhaz\JSONPointer\Parser\Lexer;

/**
 * Is thrown by lexical analyzer on recognized lexical errors. Code
 * of exception contains position of error in string.
 *
 * @package JSONPointer
 */
class SyntaxException extends RuntimeException implements \Remorhaz\JSONPointer\SyntaxException
{
}
