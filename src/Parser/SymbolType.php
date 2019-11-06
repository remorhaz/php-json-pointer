<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Parser;

final class SymbolType
{

    public const NT_ROOT = 0x00;

    public const T_SLASH = 0x01;
    public const T_TILDE = 0x02;
    public const T_ZERO = 0x03;
    public const T_ONE = 0x04;
    public const T_UNESCAPED = 0x05;

    public const NT_POINTER = 0x80;
    public const NT_REFERENCE_LIST = 0x81;
    public const NT_REFERENCE = 0x82;
    public const NT_REFERENCE_PART = 0x83;
    public const NT_UNESCAPED = 0x84;
    public const NT_ESCAPED = 0x85;
    public const NT_ESCAPED_SYMBOL = 0x86;

    public const T_EOI = 0xFF;

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
