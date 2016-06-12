<?php

namespace Remorhaz\JSONPointer\Parser\Lexer\Scanner;

/**
 * Is thrown by lexical analyzer's text scanner on PREG errors after matching
 * regular expressions. Though not all of them are "runtime", the most expected
 * error condition is broken UTF-8 in source text. Source text for scanner
 * is provided by end user, so this error is obviously a runtime problem.
 *
 * @package JSONPointer
 */
class RegExpException extends RuntimeException
{
}
