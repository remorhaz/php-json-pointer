<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Parser\TokenType;
use Remorhaz\JSON\Pointer\TokenMatcher;
use Remorhaz\UniLex\IO\CharBuffer;
use Remorhaz\UniLex\Lexer\TokenFactoryInterface;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

#[CoversClass(TokenMatcher::class)]
class TokenMatcherTest extends TestCase
{
    /**
     * @param list<int> $data
     */
    #[DataProvider('providerInvalidData')]
    public function testMatch_InvalidDataInBuffer_ReturnsFalse(array $data): void
    {
        $matcher = new TokenMatcher();
        $actualValue = $matcher->match(
            new CharBuffer(...$data),
            self::createStub(TokenFactoryInterface::class),
        );
        self::assertFalse($actualValue);
    }

    /**
     * @return iterable<string, array{list<int>}>
     */
    public static function providerInvalidData(): iterable
    {
        return [
            'Empty buffer' => [[]],
            'Non-Unicode character' => [[0x110000]],
        ];
    }

    #[DataProvider('providerValidData')]
    public function testMatch_ValidDataInBuffer_ReturnsMatchingTokens(string $data, int $token): void
    {
        $tokenFactory = $this->createMock(TokenFactoryInterface::class);
        $matcher = new TokenMatcher();

        $tokenFactory
            ->expects(self::once())
            ->method('createToken')
            ->with($token);
        $matcher->match(
            CharBufferFactory::createFromString($data),
            $tokenFactory,
        );
    }

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function providerValidData(): iterable
    {
        return [
            'Single unescaped character' => ["\u{00}", TokenType::UNESCAPED],
            'Several unescaped characters' => ["ab", TokenType::UNESCAPED],
            'Several unescaped characters followed by tilde' => [
                "\u{00}\u{2E}\u{32}\u{7D}\u{7F}\u{10FFFF}~",
                TokenType::UNESCAPED,
            ],
            'Slash followed by unescaped character' => ["/a", TokenType::SLASH],
            'Tilde followed by unescaped character' => ["~a", TokenType::TILDE],
            'Zero followed by unescaped character' => ["0a", TokenType::ZERO],
            'One followed by unescaped character' => ["1a", TokenType::ONE],
        ];
    }
}
