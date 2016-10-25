<?php

namespace Remorhaz\JSONPointer\Test\Locator\Reference;

use Remorhaz\JSONPointer\Locator\Reference;

/**
 * @package JSONPointer
 */
class ValueTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @expectedException \Remorhaz\JSONPointer\Locator\Exception
     */
    public function testAccessingUninitializedValueThrowsException()
    {
        Reference::factory()->getKey();
    }


    /**
     * @expectedException \LogicException
     */
    public function testAccessingUninitializedValueThrowsSplException()
    {
        Reference::factory()->getKey();
    }


    /**
     */
    public function testGotValueSameAsSet()
    {
        $value = 'abc';
        $reference = Reference::factory()->setKey($value);
        $this->assertEquals($value, $reference->getKey(), "Got value differs from the one that was set");
    }
}
