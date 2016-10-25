<?php

namespace Remorhaz\JSONPointer\Test\Parser\Token;

use Remorhaz\JSONPointer\Parser\Token;

class PositionTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @expectedException \Remorhaz\JSONPointer\Parser\Exception
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
     * @expectedException \Remorhaz\JSONPointer\Parser\Exception
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
