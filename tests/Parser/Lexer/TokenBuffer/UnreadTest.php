<?php

namespace Remorhaz\JSONPointer\Test\Parser\Lexer\TokenBuffer;

use Remorhaz\JSONPointer\Parser\Lexer\TokenBuffer;
use Remorhaz\JSONPointer\Parser\Token;

class UnreadTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param int $tokenAmount
     * @dataProvider providerTokenAmount
     */
    public function testUnreadingAllReadTokensNoException($tokenAmount)
    {
        $buffer = TokenBuffer::factory();
        for ($i = 0; $i < $tokenAmount; $i++) {
            $token = Token::factory(Token::TYPE_SLASH, "/", 1);
            $buffer->addToken($token);
        }
        for ($i = 0; $i < $tokenAmount; $i++) {
            $buffer->readToken();
        }
        for ($i = 0; $i < $tokenAmount; $i++) {
            $buffer->unreadToken();
        }
    }


    /**
     * @param int $tokenAmount
     * @dataProvider providerTokenAmount
     * @expectedException \Remorhaz\JSONPointer\Parser\Lexer\Exception
     */
    public function testUnreadingMoreTokensThanWasReadThrowsException($tokenAmount)
    {
        $buffer = TokenBuffer::factory();
        for ($i = 0; $i < $tokenAmount; $i++) {
            $token = Token::factory(Token::TYPE_SLASH, "/", 1);
            $buffer->addToken($token);
        }
        for ($i = 0; $i < $tokenAmount; $i++) {
            $buffer->readToken();
        }
        for ($i = 0; $i <= $tokenAmount; $i++) {
            $buffer->unreadToken();
        }
    }


    /**
     * @param int $tokenAmount
     * @dataProvider providerTokenAmount
     * @expectedException \LogicException
     */
    public function testUnreadingMoreTokensThanWasReadThrowsSplException($tokenAmount)
    {
        $buffer = TokenBuffer::factory();
        for ($i = 0; $i < $tokenAmount; $i++) {
            $token = Token::factory(Token::TYPE_SLASH, "/", 1);
            $buffer->addToken($token);
        }
        for ($i = 0; $i < $tokenAmount; $i++) {
            $buffer->readToken();
        }
        for ($i = 0; $i <= $tokenAmount; $i++) {
            $buffer->unreadToken();
        }
    }


    /**
     * @param int $tokenAmount
     * @dataProvider providerTokenAmount
     */
    public function testNoEndAfterUnreadingLastToken($tokenAmount)
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


    public function providerTokenAmount()
    {
        return [
            'singleToken' => [1],
            'multipleTokens' => [3],
        ];
    }


    /**
     */
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


    /**
     * @expectedException \Remorhaz\JSONPointer\Parser\Lexer\Exception
     */
    public function testUninitializedUnreadingThrowsException()
    {
        TokenBuffer::factory()->unreadToken();
    }


    /**
     * @expectedException \LogicException
     */
    public function testUninitializedUnreadingThrowsSplException()
    {
        TokenBuffer::factory()->unreadToken();
    }
}
