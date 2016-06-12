<?php

namespace Remorhaz\JSONPointer\Test\Locator\Reference;

use Remorhaz\JSONPointer\Locator\Reference;

/**
 * @package JSONPointer
 */
class ValueTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @expectedException \Remorhaz\JSONPointer\Locator\Reference\Exception
     */
    public function testAccessingUninitializedValueThrowsException()
    {
        Reference::factory()->getValue();
    }


    /**
     * @expectedException \LogicException
     */
    public function testAccessingUninitializedValueThrowsSplException()
    {
        Reference::factory()->getValue();
    }


    /**
     */
    public function testGotValueSameAsSet()
    {
        $value = 'abc';
        $reference = Reference::factory()->setValue($value);
        $this->assertEquals($value, $reference->getValue(), "Got value differs from the one that was set");
    }


    /**
     * @expectedException \Remorhaz\JSONPointer\Locator\Reference\Exception
     */
    public function testSettingNotStringValueThrowsException()
    {
        Reference::factory()->setValue(1);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSettingNotStringValueThrowsSplException()
    {
        Reference::factory()->setValue(1);
    }
}
