<?php

namespace Remorhaz\JSON\Pointer\Test\Parser\Lexer;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Parser\Lexer\LengthException as LexerLengthException;
use Remorhaz\JSON\Pointer\Parser\Lexer\Lexer;
use Remorhaz\JSON\Pointer\Parser\Lexer\RuntimeException as LexerRuntimeException;
use Remorhaz\JSON\Pointer\Parser\Lexer\SyntaxException as LexerSyntaxException;
use Remorhaz\JSON\Pointer\Parser\Token;

class ReadTest extends TestCase
{

    public function testUninitializedIsEndThrowsException()
    {
        $lexer = Lexer::factory();
        $this->expectException(LexerRuntimeException::class);
        $lexer->isEnd();
    }

    public function testUninitializedReadingThrowsException()
    {
        $lexer = Lexer::factory();
        $this->expectException(LexerRuntimeException::class);
        $lexer->readToken();
    }

    /**
     * @param int $tokenAmount
     * @param string $text
     * @dataProvider providerTokenAmountAndText
     */
    public function testIsEndAfterReadingAllTokensFromBuffer(int $tokenAmount, string $text)
    {
        $lexer = Lexer::factory()->setText($text);
        for ($i = 0; $i < $tokenAmount; $i++) {
            $lexer->readToken();
        }
        $this->assertTrue($lexer->isEnd(), "No end condition after reading");
    }

    public function providerTokenAmountAndText(): array
    {
        return [
            'singleToken' => [1, '/'],
            'multipleTokens' => [3, 'aб日'],
        ];
    }

    /**
     * @param string $sourceText
     * @param string $tokenText
     * @param int $tokenType
     * @dataProvider providerSingleToken
     */
    public function testReadingLastToken(string $sourceText, string $tokenText, int $tokenType)
    {
        $token = Lexer::factory()
            ->setText($sourceText)
            ->readToken();
        $this->assertEquals($tokenType, $token->getType(), "Incorrect type of token");
        $this->assertEquals($tokenText, $token->getText(), "Incorrect text of token");
    }

    public function providerSingleToken(): array
    {
        return [
            'singleSlash' => ['/', '/', Token::TYPE_SLASH],
            'singleEscaped' => ['~0', '~0', Token::TYPE_ESCAPED],
            'singleUnescapedLatin' => ['a', 'a', Token::TYPE_UNESCAPED],
            'singleUnescapedCyrillic' => ['а', 'а', Token::TYPE_UNESCAPED],
            'singleUnescapedKanji' => ['日', '日', Token::TYPE_UNESCAPED],
        ];
    }

    /**
     * @param string $sourceText
     * @param string $tokenText
     * @param int $tokenType
     * @dataProvider providerMultipleTokens
     */
    public function testReadingNotLastToken(string $sourceText, string $tokenText, int $tokenType)
    {
        $token = Lexer::factory()
            ->setText($sourceText)
            ->readToken();
        $this->assertEquals($tokenType, $token->getType(), "Incorrect type of token");
        $this->assertEquals($tokenText, $token->getText(), "Incorrect text of token");
    }

    public function providerMultipleTokens(): array
    {
        return [
            'startsFromSlash' => ['/abc', '/', Token::TYPE_SLASH],
            'startsFromEscaped' => ['~0abc', '~0', Token::TYPE_ESCAPED],
            'startsFromUnescapedLatin' => ['abc', 'a', Token::TYPE_UNESCAPED],
            'startsFromUnescapedCyrillic' => ['абв', 'а', Token::TYPE_UNESCAPED],
            'startsFromUnescapedKanji' => ['日本語', '日', Token::TYPE_UNESCAPED],
        ];
    }

    /**
     * @param string $sourceText
     * @dataProvider providerInvalidText
     */
    public function testReadingInvalidTextThrowsException(string $sourceText)
    {
        $lexer = Lexer::factory()
            ->setText($sourceText);
        $this->expectException(LexerSyntaxException::class);
        $lexer->readToken();
    }

    public function providerInvalidText(): array
    {
        return [
            'invalidEscapedToken' => ['~2'],
            'brokenEscapedToken' => ['~'],
        ];
    }

    public function testReadingEmptyTextEndThrowsException()
    {
        $lexer = Lexer::factory()
            ->setText('');
        $this->expectException(LexerLengthException::class);
        $lexer->readToken();
    }
}
