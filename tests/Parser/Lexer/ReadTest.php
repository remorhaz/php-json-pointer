<?php

namespace Remorhaz\JSONPointer\Test\Parser\Lexer;

use Remorhaz\JSONPointer\Parser\Lexer;
use Remorhaz\JSONPointer\Parser\Token;

class ReadTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @expectedException \Remorhaz\JSONPointer\Parser\Lexer\Exception
     */
    public function testUninitializedIsEndThrowsException()
    {
        Lexer::factory()->isEnd();
    }


    /**
     * @expectedException \RuntimeException
     */
    public function testUninitializedIsEndThrowsSplException()
    {
        Lexer::factory()->isEnd();
    }


    /**
     * @expectedException \Remorhaz\JSONPointer\Parser\Lexer\Exception
     */
    public function testUninitializedReadingThrowsException()
    {
        Lexer::factory()->readToken();
    }


    /**
     * @expectedException \RuntimeException
     */
    public function testUninitializedReadingThrowsSplException()
    {
        Lexer::factory()->readToken();
    }


    /**
     * @param int $tokenAmount
     * @param string $text
     * @dataProvider providerTokenAmountAndText
     */
    public function testIsEndAfterReadingAllTokensFromBuffer($tokenAmount, $text)
    {
        $lexer = Lexer::factory()->setText($text);
        for ($i = 0; $i < $tokenAmount; $i++) {
            $lexer->readToken();
        }
        $this->assertTrue($lexer->isEnd(), "No end condition after reading");
    }


    public function providerTokenAmountAndText()
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
    public function testReadingLastToken($sourceText, $tokenText, $tokenType)
    {
        $token = Lexer::factory()
            ->setText($sourceText)
            ->readToken();
        $this->assertEquals($tokenType, $token->getType(), "Incorrect type of token");
        $this->assertEquals($tokenText, $token->getText(), "Incorrect text of token");
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
     * @param string $sourceText
     * @param string $tokenText
     * @param int $tokenType
     * @dataProvider providerMultipleTokens
     */
    public function testReadingNotLastToken($sourceText, $tokenText, $tokenType)
    {
        $token = Lexer::factory()
            ->setText($sourceText)
            ->readToken();
        $this->assertEquals($tokenType, $token->getType(), "Incorrect type of token");
        $this->assertEquals($tokenText, $token->getText(), "Incorrect text of token");
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
     * @param string $sourceText
     * @dataProvider providerInvalidText
     * @expectedException \Remorhaz\JSONPointer\Parser\Lexer\Exception
     */
    public function testReadingInvalidTextThrowsException($sourceText)
    {
        Lexer::factory()
            ->setText($sourceText)
            ->readToken();
    }


    /**
     * @param string $sourceText
     * @dataProvider providerInvalidText
     * @expectedException \Remorhaz\JSONPointer\SyntaxException
     * @expectedExceptionMessageRegExp / at position #\d+/
     */
    public function testReadingInvalidTextThrowsSyntaxException($sourceText)
    {
        Lexer::factory()
            ->setText($sourceText)
            ->readToken();
    }


    /**
     * @param string $sourceText
     * @dataProvider providerInvalidText
     * @expectedException \RuntimeException
     */
    public function testReadingInvalidTextThrowsSplException($sourceText)
    {
        Lexer::factory()
            ->setText($sourceText)
            ->readToken();
    }


    public function providerInvalidText()
    {
        return [
            'invalidEscapedToken' => ['~2'],
            'brokenEscapedToken' => ['~'],
        ];
    }


    /**
     * @expectedException \Remorhaz\JSONPointer\Parser\Lexer\Exception
     */
    public function testReadingEmptyTextEndThrowsException()
    {
        Lexer::factory()
            ->setText('')
            ->readToken();
    }


    /**
     * @expectedException \LengthException
     */
    public function testReadingEmptyTextEndThrowsSplException()
    {
        Lexer::factory()
            ->setText('')
            ->readToken();
    }
}
