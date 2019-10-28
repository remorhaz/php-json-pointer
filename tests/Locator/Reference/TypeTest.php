<?php

namespace Remorhaz\JSON\Pointer\Test\Locator\Reference;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\DomainException as LocatorDomainException;
use Remorhaz\JSON\Pointer\Locator\LogicException as LocatorLogicException;
use Remorhaz\JSON\Pointer\Locator\Reference;

class TypeTest extends TestCase
{

    public function testAccessingUninitializedTypeThrowsException()
    {
        $factory = Reference::factory();
        $this->expectException(LocatorLogicException::class);
        $factory->getType();
    }

    /**
     */
    public function testGotTypeSameAsSet()
    {
        $type = Reference::TYPE_INDEX;
        $reference = Reference::factory()->setType($type);
        $this->assertEquals($type, $reference->getType(), "Got type differs from the one that was set");
    }

    public function testSettingInvalidTypeThrowsException()
    {
        $factory = Reference::factory();
        $this->expectException(LocatorDomainException::class);
        $factory->setType(0xFF);
    }
}
