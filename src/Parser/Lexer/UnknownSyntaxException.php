<?php

namespace Remorhaz\JSONPointer\Parser\Lexer;

/**
 * Is thrown when lexical analyzer fails to recognize lexical error. Code
 * of exception contains position of error in string.
 *
 * @package JSONPointer
 */
class UnknownSyntaxException extends DomainException implements \Remorhaz\JSONPointer\SyntaxException
{
}
