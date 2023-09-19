<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Parser;

final class TokenType
{
    public const SLASH = 0x01;
    public const TILDE = 0x02;
    public const ZERO = 0x03;
    public const ONE = 0x04;
    public const UNESCAPED = 0x05;

    public const EOI = 0xFF;

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
