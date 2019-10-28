<?php

namespace Remorhaz\JSON\Pointer\Test\Parser\Token;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Parser\DomainException as ParserDomainException;
use Remorhaz\JSON\Pointer\Parser\LogicException;
use Remorhaz\JSON\Pointer\Parser\Token;

class TypeTest extends TestCase
{

    public function testAccessingUninitializedTypeThrowsException()
    {
        $token = Token::factory();
        $this->expectException(LogicException::class);
        $token->getType();
    }

    public function testGotTypeSameAsSet()
    {
        $type = Token::TYPE_SLASH;
        $token = Token::factory()->setType($type);
        $this->assertEquals($type, $token->getType(), "Got type differs from the one that was set");
    }

    public function testSettingInvalidTypeThrowsException()
    {
        $token = Token::factory();
        $this->expectException(ParserDomainException::class);
        $token->setType(0xFF);
    }

    public function testIsErrorAfterSettingErrorType()
    {
        $token = Token::factory()->setType(Token::TYPE_ERROR_INVALID_ESCAPE);
        $this->assertTrue($token->isError(), "No error in token after setting error type");
    }

    public function testNoErrorAfterSettingNonErrorType()
    {
        $token = Token::factory()->setType(Token::TYPE_SLASH);
        $this->assertFalse($token->isError(), "Error in token after setting non-error type");
    }
}
