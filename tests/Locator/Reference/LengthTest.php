<?php

namespace Remorhaz\JSON\Pointer\Test\Locator\Reference;

use Remorhaz\JSON\Pointer\Locator\Reference;

class LengthTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @expectedException \Remorhaz\JSON\Pointer\Locator\Exception
     */
    public function testAccessingUninitializedLengthThrowsException()
    {
        Reference::factory()->getLength();
    }


    /**
     * @expectedException \LogicException
     */
    public function testAccessingUninitializedLengthThrowsSplException()
    {
        Reference::factory()->getLength();
    }


    /**
     * @param int $length
     * @dataProvider providerValidLength
     */
    public function testGotLengthSameAsSet(int $length)
    {
        $reference = Reference::factory()->setLength($length);
        $this->assertEquals($length, $reference->getLength(), "Got length differs from set one");
    }


    public function providerValidLength(): array
    {
        return [
            'zeroLength' => [0],
            'oneLength' => [1],
            'maxLength' => [PHP_INT_MAX],
        ];
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\Locator\Exception
     */
    public function testSetNegativeLengthThrowsException()
    {
        Reference::factory()->setLength(-1);
    }


    /**
     * @expectedException \DomainException
     */
    public function testSetNegativeLengthThrowsSplException()
    {
        Reference::factory()->setLength(-1);
    }
}
