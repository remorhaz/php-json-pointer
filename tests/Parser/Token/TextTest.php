<?php

namespace Remorhaz\JSON\Pointer\Test\Parser\Token;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Parser\LengthException as ParserLengthException;
use Remorhaz\JSON\Pointer\Parser\LogicException as ParserLogicException;
use Remorhaz\JSON\Pointer\Parser\Token;

class TextTest extends TestCase
{

    public function testAccessingUninitializedTextThrowsException()
    {
        $token = Token::factory();
        $this->expectException(ParserLogicException::class);
        $token->getText();
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

    public function testSettingEmptyTextThrowsException()
    {
        $token = Token::factory();
        $this->expectException(ParserLengthException::class);
        $token->setText('');
    }
}
