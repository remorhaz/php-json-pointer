<?php

namespace Remorhaz\JSON\Pointer\Test\Parser\Token;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Parser\DomainException;
use Remorhaz\JSON\Pointer\Parser\LogicException as ParserLogicException;
use Remorhaz\JSON\Pointer\Parser\Token;

class PositionTest extends TestCase
{

    public function testAccessingUninitializedPositionThrowsException()
    {
        $token = Token::factory();
        $this->expectException(ParserLogicException::class);
        $token->getPosition();
    }

    /**
     * @param int $position
     * @dataProvider providerPosition
     */
    public function testGotPositionSameAsSet(int $position)
    {
        $token = Token::factory()->setPosition($position);
        $this->assertEquals($position, $token->getPosition(), "Got position differs from the one that was set");
    }

    public function providerPosition(): array
    {
        return [
            'zero' => [0],
            'one' => [1],
            'max' => [PHP_INT_MAX],
        ];
    }

    public function testSettingNegativePositionThrowsException()
    {
        $token = Token::factory();
        $this->expectException(DomainException::class);
        $token->setPosition(-1);
    }
}
