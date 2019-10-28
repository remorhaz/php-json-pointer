<?php

namespace Remorhaz\JSON\Pointer\Test\Parser\Lexer;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Parser\Lexer\Lexer;
use Remorhaz\JSON\Pointer\Parser\Lexer\RegExpException;
use Remorhaz\JSON\Pointer\Parser\Token;

class TextTest extends TestCase
{

    public function testSettingTextResetsBuffer()
    {
        $lexer = Lexer::factory()
            ->setText('/');
        $lexer->readToken();
        $token = $lexer
            ->unreadToken() // Now lexer`s buffer contains slash and is not at end.
            ->setText('a')
            ->readToken(); // If buffer was not reset, this will read slash from old buffer, otherwise - unescaped 'a'.
        $this->assertEquals(
            Token::TYPE_UNESCAPED,
            $token->getType(),
            "Lexical analyzer buffer was not reset by setting new source text"
        );
    }

    public function testIsEndAfterSettingEmptyText()
    {
        $lexer = Lexer::factory()->setText('');
        $this->assertTrue($lexer->isEnd(), "End of text is not reached after setting empty string");
    }

    public function testIsNotEndAfterSettingNonEmptyText()
    {
        $lexer = Lexer::factory()->setText('abc');
        $this->assertFalse($lexer->isEnd(), "End of text is reached after setting non-empty string");
    }

    /**
     * @param string $text
     * @dataProvider providerBrokenUnicodeText
     */
    public function testSettingBrokenUnicodeTextThrowsException(string $text)
    {
        $lexer = Lexer::factory();
        $this->expectException(RegExpException::class);
        $lexer->setText($text);
    }

    public function providerBrokenUnicodeText(): array
    {
        return [
            'singleBrokenUnicode' => [substr('日', -1, 1)],
            'containsBrokenUnicode' => ['аб' . substr('в', -1, 1) . 'г'],
        ];
    }
}
