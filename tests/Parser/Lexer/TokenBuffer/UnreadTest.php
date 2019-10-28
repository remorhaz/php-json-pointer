<?php

namespace Remorhaz\JSON\Pointer\Test\Parser\Lexer\TokenBuffer;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Parser\Lexer\LogicException as LexerLogicException;
use Remorhaz\JSON\Pointer\Parser\Lexer\TokenBuffer;
use Remorhaz\JSON\Pointer\Parser\Token;

class UnreadTest extends TestCase
{

    /**
     * @param int $tokenAmount
     * @dataProvider providerTokenAmount
     */
    public function testUnreadingMoreTokensThanWasReadThrowsSplException(int $tokenAmount)
    {
        $buffer = TokenBuffer::factory();
        for ($i = 0; $i < $tokenAmount; $i++) {
            $token = Token::factory(Token::TYPE_SLASH, "/", 1);
            $buffer->addToken($token);
        }
        for ($i = 0; $i < $tokenAmount; $i++) {
            $buffer->readToken();
        }
        $this->expectException(LexerLogicException::class);
        for ($i = 0; $i <= $tokenAmount; $i++) {
            $buffer->unreadToken();
        }
    }


    /**
     * @param int $tokenAmount
     * @dataProvider providerTokenAmount
     */
    public function testNoEndAfterUnreadingLastToken(int $tokenAmount)
    {
        $buffer = TokenBuffer::factory();
        for ($i = 0; $i < $tokenAmount; $i++) {
            $token = Token::factory(Token::TYPE_SLASH, "/", 1);
            $buffer->addToken($token);
        }
        for ($i = 0; $i < $tokenAmount; $i++) {
            $buffer->readToken();
        }
        $buffer->unreadToken();
        $this->assertFalse($buffer->isEnd(), "End condition after unreading token to buffer");
    }


    public function providerTokenAmount(): array
    {
        return [
            'singleToken' => [1],
            'multipleTokens' => [3],
        ];
    }


    public function testReadingSameTokenAfterUnreading()
    {
        $buffer = TokenBuffer::factory();
        $token = Token::factory(Token::TYPE_SLASH, "/", 1);
        $buffer->addToken($token);
        $token = Token::factory(Token::TYPE_UNESCAPED, "a", 1, "a");
        $buffer->addToken($token);
        $firstToken = $buffer->readToken();
        $secondToken = $buffer
            ->unreadToken()
            ->readToken();
        $this->assertSame($firstToken, $secondToken, "Reading after unreading returns different tokens");
    }

    public function testUninitializedUnreadingThrowsException()
    {
        $buffer = TokenBuffer::factory();
        $this->expectException(LexerLogicException::class);
        $buffer->unreadToken();
    }
}
