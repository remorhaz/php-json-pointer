<?php

namespace Remorhaz\JSON\Pointer\Test\Parser\Token;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Parser\Token;

class PositionTest extends TestCase
{


    /**
     * @expectedException \Remorhaz\JSON\Pointer\Parser\Exception
     */
    public function testAccessingUninitializedPositionThrowsException()
    {
        Token::factory()->getPosition();
    }


    /**
     * @expectedException \LogicException
     */
    public function testAccessingUninitializedPositionThrowsSplException()
    {
        Token::factory()->getPosition();
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


    /**
     * @expectedException \Remorhaz\JSON\Pointer\Parser\Exception
     */
    public function testSettingNegativePositionThrowsException()
    {
        Token::factory()->setPosition(-1);
    }


    /**
     * @expectedException \DomainException
     */
    public function testSettingNegativePositionThrowsSplException()
    {
        Token::factory()->setPosition(-1);
    }
}
