<?php

namespace Remorhaz\JSON\Pointer\Test\Locator\Reference;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\LogicException as LocatorLogicException;
use Remorhaz\JSON\Pointer\Locator\Reference;

class IsLastTest extends TestCase
{

    public function testAccessingUninitializedIsLastThrowsException()
    {
        $factory = Reference::factory();
        $this->expectException(LocatorLogicException::class);
        $factory->isLast();
    }
}
