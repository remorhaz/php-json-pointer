<?php

namespace Remorhaz\JSONPointer\Test\Parser\Lexer\TokenBuffer;

use Remorhaz\JSONPointer\Parser\Lexer\TokenBuffer;
use Remorhaz\JSONPointer\Parser\Token;

class AddTest extends \PHPUnit_Framework_TestCase
{


    /**
     */
    public function testTokenAddedInEmptyBufferGetsZeroPosition(): TokenBuffer
    {
        $token = Token::factory(Token::TYPE_SLASH, "/", 1);
        $buffer = TokenBuffer::factory()->addToken($token);
        $this->assertEquals(0, $token->getPosition(), "Incorrect position of token added to empty buffer");
        return $buffer;
    }


    /**
     * @param TokenBuffer $buffer
     * @depends testTokenAddedInEmptyBufferGetsZeroPosition
     */
    public function testTokenAddedInNonEmptyBufferGetsNonZeroPosition(TokenBuffer $buffer)
    {
        $token = Token::factory(Token::TYPE_SLASH, "/", 1);
        $buffer->addToken($token);
        $this->assertNotEquals(0, $token->getPosition(), "Incorrect position of token added to empty buffer");
    }


    /**
     * @param int $tokenAmount
     * @dataProvider providerTokenAmount
     */
    public function testNotIsEndAfterAddingToken(int $tokenAmount)
    {
        $buffer = TokenBuffer::factory();
        for ($i = 0; $i < $tokenAmount; $i++) {
            $token = Token::factory(Token::TYPE_SLASH, "/", 1);
            $buffer->addToken($token);
        }
        $this->assertFalse($buffer->isEnd(), "End condition after adding token to buffer");
    }


    public function providerTokenAmount(): array
    {
        return [
            'singleToken' => [1],
            'multipleTokens' => [3],
        ];
    }
}
