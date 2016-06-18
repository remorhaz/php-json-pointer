<?php

namespace Remorhaz\JSONPointer\Test\Locator\Reference;

use Remorhaz\JSONPointer\Pointer\Locator\Reference;

class LengthTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @expectedException \Remorhaz\JSONPointer\Pointer\Locator\Exception
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
    public function testGotLengthSameAsSet($length)
    {
        $reference = Reference::factory()->setLength($length);
        $this->assertEquals($length, $reference->getLength(), "Got length differs from set one");
    }


    public function providerValidLength()
    {
        return [
            'zeroLength' => [0],
            'oneLength' => [1],
            'maxLength' => [PHP_INT_MAX],
        ];
    }


    /**
     * @expectedException \Remorhaz\JSONPointer\Pointer\Locator\Exception
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


    /**
     * @expectedException \Remorhaz\JSONPointer\Pointer\Locator\Exception
     */
    public function testSetNonIntegerLengthThrowsException()
    {
        Reference::factory()->setLength("5");
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetNonIntegerLengthThrowsSplException()
    {
        Reference::factory()->setLength("5");
    }
}
