<?php

namespace Remorhaz\JSON\Pointer\Test\Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Parser\LogicException as ParserLogicException;
use Remorhaz\JSON\Pointer\Parser\ReferenceBuffer;
use Remorhaz\JSON\Pointer\Parser\Token;

class ReferenceBufferTest extends TestCase
{

    /**
     * @param Token[] $tokenList
     * @param int $length
     * @param string $value
     * @dataProvider providerValidTokens
     */
    public function testAddTokenUpdatesLengthAndValue(array $tokenList, int $length, string $value)
    {
        $buffer = ReferenceBuffer::factory();
        foreach ($tokenList as $token) {
            $buffer->addToken($token);
        }
        $reference = $buffer
            ->flush()
            ->getReference();
        $this->assertEquals($length, $reference->getLength(), "Incorrect reference length built from token list");
        $this->assertEquals($value, $reference->getKey(), "Incorrect reference value built from token list");
    }

    public function providerValidTokens(): array
    {
        return [
            'noTokens' => [[], 0, ""],
            'unescapedToken' => [
                [Token::factory(Token::TYPE_UNESCAPED, "a", 1, "a")],
                1,
                "a",
            ],
            'escapedToken' => [
                [Token::factory(Token::TYPE_ESCAPED, "~1", 2, "/")],
                2,
                "/",
            ],
            'multipleTokens' => [
                [
                    Token::factory(Token::TYPE_UNESCAPED, "a", 1, "a"),
                    Token::factory(Token::TYPE_ESCAPED, "~0", 2, "~"),
                ],
                3,
                "a~",
            ],
        ];
    }

    public function testAddTokenAfterFlushWithoutResetThrowsException()
    {
        $token = Token::factory(Token::TYPE_UNESCAPED, "x", 1, "x");
        $buffer = ReferenceBuffer::factory()
            ->addToken($token)
            ->flush();
        $token = Token::factory(Token::TYPE_UNESCAPED, "y", 1, "y");
        $this->expectException(ParserLogicException::class);
        $buffer->addToken($token);
    }

    public function testAddTokenAfterFlush()
    {
        $token = Token::factory(Token::TYPE_UNESCAPED, "x", 1, "x");
        $buffer = ReferenceBuffer::factory()
            ->addToken($token)
            ->flush()
            ->reset();
        $token = Token::factory(Token::TYPE_UNESCAPED, "y", 1, "y");
        $reference = $buffer
            ->addToken($token)
            ->flush()
            ->getReference();
        $this->assertEquals(
            'y',
            $reference->getKey(),
            "Incorrect reference value built from tokens added after resetting flushed buffer"
        );
    }

    public function testFlushingUninitializedBufferProducesEmptyPropertyReference()
    {
        $reference = ReferenceBuffer::factory()
            ->flush()
            ->getReference();
        $this->assertSame(
            "",
            $reference->getKey(),
            "Incorrect reference value built from uninitialized token buffer"
        );
    }

    public function testDoubleFlushThrowsException()
    {
        $buffer = ReferenceBuffer::factory()
            ->flush();
        $this->expectException(ParserLogicException::class);
        $buffer->flush();
    }

    public function testGetUninitializedReferenceThrowsException()
    {
        $buffer = ReferenceBuffer::factory();
        $this->expectException(ParserLogicException::class);
        $buffer->getReference();
    }

    public function testGetReferenceAfterResetThrowsException()
    {
        $buffer = ReferenceBuffer::factory()
            ->flush()
            ->reset();
        $this->expectException(ParserLogicException::class);
        $buffer->getReference();
    }
}
