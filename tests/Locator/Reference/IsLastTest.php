<?php

namespace Remorhaz\JSONPointer\Test\Locator\Reference;

use Remorhaz\JSONPointer\Locator\Reference;

class IsLastTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @expectedException \Remorhaz\JSONPointer\Locator\Reference\Exception
     */
    public function testAccessingUninitializedIsLastThrowsException()
    {
        Reference::factory()->isLast();
    }


    /**
     * @expectedException \LogicException
     */
    public function testAccessingUninitializedIsLastThrowsSplException()
    {
        Reference::factory()->isLast();
    }


    /**
     * @param bool $on
     * @dataProvider providerGotIsLastSameAsSet
     */
    public function testGotIsLastSameAsSet($on)
    {
        $reference = Reference::factory()->setLast($on);
        $this->assertSame($on, $reference->isLast(), "Got last reference flag differs from the one that was set");
    }


    public function providerGotIsLastSameAsSet()
    {
        return [
            'true' => [true],
            'false' => [false],
        ];
    }


    /**
     * @expectedException \Remorhaz\JSONPointer\Locator\Reference\Exception
     */
    public function testSetNotBooleanLastThrowsException()
    {
        Reference::factory()->setLast(0);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetNotBooleanLastThrowsSplException()
    {
        Reference::factory()->setLast(0);
    }
}
