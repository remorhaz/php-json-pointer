<?php

namespace Remorhaz\JSONPointer\Test\Parser\Token;

use Remorhaz\JSONPointer\Parser\Token;

class TextTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @expectedException \Remorhaz\JSONPointer\Parser\Token\Exception
     */
    public function testAccessingUninitializedTextThrowsException()
    {
        Token::factory()->getText();
    }


    /**
     * @expectedException \LogicException
     */
    public function testAccessingUninitializedTextThrowsSplException()
    {
        Token::factory()->getText();
    }


    /**
     * @param string $text
     * @dataProvider providerText
     */
    public function testGotTextSameAsSet($text)
    {
        $token = Token::factory()->setText($text);
        $this->assertSame($text, $token->getText(), "Read text is not the same as set one");
    }


    public function providerText()
    {
        return [
            'slashToken' => ['/'],
            'escapedToken' => ['~0'],
            'unescapedTokenWhitespaces' => [" \r\n\f\t\v"],
            'unescapedTokenLatinChars' => ['abc'],
            'unescapedTokenCyrillicChars' => ['абв'],
            'unescapedTokenKanjiChars' => ['日本語'],
            'unescapedTokenMixedChars' => ["a\tб 日~1"],
        ];
    }


    /**
     * @expectedException \Remorhaz\JSONPointer\Parser\Token\Exception
     */
    public function testSettingEmptyTextThrowsException()
    {
        Token::factory()->setText('');
    }


    /**
     * @expectedException \LengthException
     */
    public function testSettingEmptyTextThrowsSplException()
    {
        Token::factory()->setText('');
    }


    public function providerBrokenUnicodeText()
    {
        return [
            'singleBrokenUnicode' => [substr('日', -1, 1)],
            'containsBrokenUnicode' => ['аб' . substr('в', -1, 1) . 'г'],
        ];
    }


    /**
     * @expectedException \Remorhaz\JSONPointer\Parser\Token\Exception
     */
    public function testSettingNonStringTextThrowsException()
    {
        Token::factory()->setText(1);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSettingNonStringTextThrowsSplException()
    {
        Token::factory()->setText(1);
    }
}
