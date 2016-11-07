<?php

namespace Remorhaz\JSON\Pointer\Parser\Lexer;

/**
 * Is thrown when lexical analyzer fails to recognize lexical error. Code
 * of exception contains position of error in string.
 */
class UnknownSyntaxException extends DomainException implements \Remorhaz\JSON\Pointer\SyntaxException
{
}
