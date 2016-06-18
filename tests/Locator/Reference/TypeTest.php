<?php

namespace Remorhaz\JSONPointer\Test\Locator\Reference;

use Remorhaz\JSONPointer\Locator\Reference;

/**
 * @package JSONPointer
 */
class TypeTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @expectedException \Remorhaz\JSONPointer\Locator\Exception
     */
    public function testAccessingUninitializedTypeThrowsException()
    {
        Reference::factory()->getType();
    }


    /**
     * @expectedException \LogicException
     */
    public function testAccessingUninitializedTypeThrowsSplException()
    {
        Reference::factory()->getType();
    }


    /**
     */
    public function testGotTypeSameAsSet()
    {
        $type = Reference::TYPE_INDEX;
        $reference = Reference::factory()->setType($type);
        $this->assertEquals($type, $reference->getType(), "Got type differs from the one that was set");
    }


    /**
     * @expectedException \Remorhaz\JSONPointer\Locator\Exception
     */
    public function testSettingInvalidTypeThrowsException()
    {
        Reference::factory()->setType(0xFF);
    }


    /**
     * @expectedException \DomainException
     */
    public function testSettingInvalidTypeThrowsSplException()
    {
        Reference::factory()->setType(0xFF);
    }


    /**
     * @expectedException \Remorhaz\JSONPointer\Locator\Exception
     */
    public function testSettingNotIntegerThrowsException()
    {
        Reference::factory()->setType((string) Reference::TYPE_INDEX);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSettingNotIntegerTypeThrowsSplException()
    {
        Reference::factory()->setType((string) Reference::TYPE_INDEX);
    }
}
