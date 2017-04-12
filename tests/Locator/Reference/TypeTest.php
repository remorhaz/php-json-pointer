<?php

namespace Remorhaz\JSON\Pointer\Test\Locator\Reference;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\Reference;

class TypeTest extends TestCase
{


    /**
     * @expectedException \Remorhaz\JSON\Pointer\Locator\Exception
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
     * @expectedException \Remorhaz\JSON\Pointer\Locator\Exception
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
}
