<?php

namespace Remorhaz\JSONPointer\Parser\Lexer\Scanner;

/**
 * Is thrown when lexical analyzer's text scanner finds unknown lexical error,
 * i. e. completely fails to match next token. Known lexical errors are
 * passed as error tokens and are processed at lexical analyzer's level.
 *
 * @package JSONPointer
 */
class UnknownSyntaxException extends DomainException implements \Remorhaz\JSONPointer\SyntaxException
{
}
