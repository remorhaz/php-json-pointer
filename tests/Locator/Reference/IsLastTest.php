<?php

namespace Remorhaz\JSON\Pointer\Test\Locator\Reference;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\Reference;

class IsLastTest extends TestCase
{


    /**
     * @expectedException \Remorhaz\JSON\Pointer\Locator\Exception
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
}
