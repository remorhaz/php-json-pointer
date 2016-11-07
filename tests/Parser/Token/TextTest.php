<?php

namespace Remorhaz\JSON\Pointer\Test\Parser\Token;

use Remorhaz\JSON\Pointer\Parser\Token;

class TextTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @expectedException \Remorhaz\JSON\Pointer\Parser\Exception
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
    public function testGotTextSameAsSet(string $text)
    {
        $token = Token::factory()->setText($text);
        $this->assertSame($text, $token->getText(), "Read text is not the same as set one");
    }


    public function providerText(): array
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
     * @expectedException \Remorhaz\JSON\Pointer\Parser\Exception
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
}
