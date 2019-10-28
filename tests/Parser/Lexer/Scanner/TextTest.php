<?php

namespace Remorhaz\JSON\Pointer\Test\Parser\Lexer\Scanner;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Parser\Lexer\RegExpException;
use Remorhaz\JSON\Pointer\Parser\Lexer\RuntimeException as LexerRuntimeException;
use Remorhaz\JSON\Pointer\Parser\Lexer\Scanner;
use const PREG_BAD_UTF8_ERROR;

/**
 */
class TextTest extends TestCase
{

    public function testUninitializedTextAccessThrowsException()
    {
        $scanner = Scanner::factory();
        $this->expectException(LexerRuntimeException::class);
        $scanner->getText();
    }

    /**
     * @param string $text
     * @dataProvider providerText
     */
    public function testGotTextSameAsSet(string $text)
    {
        $scanner = Scanner::factory()->setText($text);
        $this->assertEquals($text, $scanner->getText(), "Got text differs from the one that was set");
    }

    public function providerText(): array
    {
        return [
            'emptyText' => [''],
            'whitespaceText' => [" \r\n\f\t\v"],
            'latinText' => ['abc'],
            'cyrillicText' => ['абв'],
            'kanjiText' => ['日本語'],
            'mixedText' => ["a\tб 日~1"],
        ];
    }

    /**
     */
    public function testIsEndAfterSettingEmptyText()
    {
        $scanner = Scanner::factory()->setText('');
        $this->assertTrue($scanner->isEnd(), "End of text is not reached after setting empty string");
    }

    /**
     */
    public function testIsNotEndAfterSettingNonEmptyText()
    {
        $scanner = Scanner::factory()->setText('abc');
        $this->assertFalse($scanner->isEnd(), "End of text is reached after setting non-empty string");
    }

    /**
     * @param string $text
     * @dataProvider providerBrokenUnicodeText
     */
    public function testSettingBrokenUnicodeTextThrowsException(string $text)
    {
        $scanner = Scanner::factory();
        $this->expectException(RegExpException::class);
        $this->expectExceptionCode(PREG_BAD_UTF8_ERROR);
        $this->expectExceptionMessage('PREG_BAD_UTF8_ERROR');
        $scanner->setText($text);
    }

    public function providerBrokenUnicodeText(): array
    {
        return [
            'singleBrokenUnicode' => [substr('日', -1, 1)],
            'containsBrokenUnicode' => ['аб' . substr('в', -1, 1) . 'г'],
        ];
    }
}
