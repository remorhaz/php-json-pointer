<?php

namespace Remorhaz\JSON\Pointer\Test\Parser\Lexer\TokenBuffer;

use Remorhaz\JSON\Pointer\Parser\Lexer\TokenBuffer;
use Remorhaz\JSON\Pointer\Parser\Token;

class ReadTest extends \PHPUnit_Framework_TestCase
{


    /**
     */
    public function testIsEndInitially()
    {
        $buffer = TokenBuffer::factory();
        $this->assertTrue($buffer->isEnd(), "No end condition after creation of buffer");
    }


    /**
     * @param int $tokenAmount
     * @dataProvider providerTokenAmount
     */
    public function testReadTokenIsFirstAdded(int $tokenAmount)
    {
        $buffer = TokenBuffer::factory();
        $firstToken = null;
        for ($i = 0; $i < $tokenAmount; $i++) {
            $token = Token::factory(Token::TYPE_SLASH, "/", 1);
            if (null === $firstToken) {
                $firstToken = $token;
            }
            $buffer->addToken($token);
        }
        $this->assertSame(
            $firstToken,
            $buffer->readToken(),
            "First token read from buffer is not the one that was added first"
        );
    }


    /**
     * @param int $tokenAmount
     * @dataProvider providerTokenAmount
     */
    public function testIsEndAfterReadingAllTokensFromBuffer(int $tokenAmount)
    {
        $buffer = TokenBuffer::factory();
        for ($i = 0; $i < $tokenAmount; $i++) {
            $token = Token::factory(Token::TYPE_SLASH, "/", 1);
            $buffer->addToken($token);
        }
        for ($i = 0; $i < $tokenAmount; $i++) {
            $buffer->readToken();
        }
        $this->assertTrue($buffer->isEnd(), "No end condition after reading");
    }


    public function providerTokenAmount(): array
    {
        return [
            'singleToken' => [1],
            'multipleTokens' => [3],
        ];
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\Parser\Lexer\Exception
     */
    public function testUninitializedReadingThrowsException()
    {
        TokenBuffer::factory()->readToken();
    }


    /**
     * @expectedException \LogicException
     */
    public function testUninitializedReadingThrowsSplException()
    {
        TokenBuffer::factory()->readToken();
    }
}
