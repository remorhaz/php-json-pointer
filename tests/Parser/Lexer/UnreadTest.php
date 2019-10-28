<?php

namespace Remorhaz\JSON\Pointer\Test\Parser\Lexer;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Parser\Lexer\Lexer;
use Remorhaz\JSON\Pointer\Parser\Lexer\LogicException as LexerLogicException;

class UnreadTest extends TestCase
{

    public function testUninitializedUnreadingThrowsException()
    {
        $lexer = Lexer::factory();
        $this->expectException(LexerLogicException::class);
        $lexer->unreadToken();
    }

    public function testReadingSameTokenAfterUnreading()
    {
        $lexer = Lexer::factory()->setText('/a');
        $firstToken = $lexer->readToken();
        $secondToken = $lexer
            ->unreadToken()
            ->readToken();
        $this->assertSame($firstToken, $secondToken, "Reading after unreading returns different tokens");
    }

    /**
     * @param int $tokenAmount
     * @param string $text
     * @dataProvider providerTokenAmountAndText
     */
    public function testUnreadingMoreTokensThanWasReadThrowsException(int $tokenAmount, string $text)
    {
        $lexer = Lexer::factory()->setText($text);
        for ($i = 0; $i < $tokenAmount; $i++) {
            $lexer->readToken();
        }
        $this->expectException(LexerLogicException::class);
        for ($i = 0; $i <= $tokenAmount; $i++) {
            $lexer->unreadToken();
        }
    }

    /**
     * @param int $tokenAmount
     * @param string $text
     * @dataProvider providerTokenAmountAndText
     */
    public function testNoEndAfterUnreadingLastToken(int $tokenAmount, string $text)
    {
        $lexer = Lexer::factory()->setText($text);
        for ($i = 0; $i < $tokenAmount; $i++) {
            $lexer->readToken();
        }
        $lexer->unreadToken();
        $this->assertFalse($lexer->isEnd(), "End condition after unreading token to buffer");
    }

    public function providerTokenAmountAndText(): array
    {
        return [
            'singleToken' => [1, '/'],
            'multipleTokens' => [3, 'aб日'],
        ];
    }
}
