<?php

namespace Remorhaz\JSON\Pointer\Test\Locator\Reference;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\LogicException as LocatorLogicException;
use Remorhaz\JSON\Pointer\Locator\Reference;

class ValueTest extends TestCase
{

    public function testAccessingUninitializedValueThrowsException()
    {
        $factory = Reference::factory();
        $this->expectException(LocatorLogicException::class);
        $factory->getKey();
    }

    public function testGotValueSameAsSet()
    {
        $value = 'abc';
        $reference = Reference::factory()->setKey($value);
        $this->assertEquals($value, $reference->getKey(), "Got value differs from the one that was set");
    }
}
