<?php

namespace Remorhaz\JSONPointer\Test\Locator\Reference;

use Remorhaz\JSONPointer\Pointer\Locator\Reference;

/**
 * @package JSONPointer
 */
class ValueTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @expectedException \Remorhaz\JSONPointer\Pointer\Locator\Exception
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


    /**
     * @expectedException \Remorhaz\JSONPointer\Pointer\Locator\Exception
     */
    public function testSettingNotStringValueThrowsException()
    {
        Reference::factory()->setKey(1);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSettingNotStringValueThrowsSplException()
    {
        Reference::factory()->setKey(1);
    }
}
