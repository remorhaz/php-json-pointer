<?php

namespace Remorhaz\JSON\Pointer\Parser\Lexer;

/**
 * Is thrown by lexical analyzer on recognized lexical errors. Code
 * of exception contains position of error in string.
 */
class SyntaxException extends RuntimeException implements \Remorhaz\JSON\Pointer\SyntaxException
{
}
