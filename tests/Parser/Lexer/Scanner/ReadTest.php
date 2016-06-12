<?php

namespace Remorhaz\JSONPointer\Test\Parser\Lexer\Scanner;

use Remorhaz\JSONPointer\Parser\Lexer\Scanner;
use Remorhaz\JSONPointer\Parser\Token;

/**
 */
class ReadTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param string $text
     * @dataProvider providerSingleToken
     */
    public function testIsEndAfterReadingLastToken($text)
    {
        $scanner = Scanner::factory()->setText($text);
        $scanner->readToken();
        $this->assertTrue($scanner->isEnd(), "No end of text after reading last token");
    }


    /**
     * @param string $text
     * @param string $tokenText
     * @param int $tokenType
     * @dataProvider providerSingleToken
     */
    public function testReadingLastToken($text, $tokenText, $tokenType)
    {
        $scanner = Scanner::factory()->setText($text);
        $token = $scanner->readToken();
        $this->assertEquals($tokenType, $token->getType(), "Incorrect token type after reading");
        $this->assertEquals($tokenText, $token->getText(), "Incorrect token text after reading");
    }


    public function providerSingleToken()
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
     * @param string $text
     * @dataProvider providerMultipleTokens
     */
    public function testNoEndAfterReadingNotLastToken($text)
    {
        $scanner = Scanner::factory()->setText($text);
        $scanner->readToken();
        $this->assertFalse($scanner->isEnd(), "End of text after reading not last token");
    }


    /**
     * @param string $text
     * @param string $tokenText
     * @param int $tokenType
     * @dataProvider providerMultipleTokens
     */
    public function testReadingNotLastToken($text, $tokenText, $tokenType)
    {
        $scanner = Scanner::factory()->setText($text);
        $token = $scanner->readToken();
        $this->assertEquals($tokenType, $token->getType(), "Imcorrect token type after reading");
        $this->assertEquals($tokenText, $token->getText(), "Incorrect token text after reading");
    }


    public function providerMultipleTokens()
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
     * @param string $text
     * @dataProvider providerInvalidText
     */
    public function testReadingErrorTokenFromInvalidText($text)
    {
        $token = Scanner::factory()
            ->setText($text)
            ->readToken();
        $this->assertTrue($token->isError(), "Token read from invalid text is not an error");
    }


    public function providerInvalidText()
    {
        return [
            'invalidEscapedToken' => ['~2'],
            'brokenEscapedToken' => ['~'],
        ];
    }


    /**
     * @expectedException \Remorhaz\JSONPointer\Parser\Lexer\Scanner\Exception
     */
    public function testReadingEmptyTextEndThrowsException()
    {
        Scanner::factory()
            ->setText('')
            ->readToken();
    }


    /**
     * @expectedException \LengthException
     */
    public function testReadingEmptyTextEndThrowsSplException()
    {
        Scanner::factory()
            ->setText('')
            ->readToken();
    }
}
