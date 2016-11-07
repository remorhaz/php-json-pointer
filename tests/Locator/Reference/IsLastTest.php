<?php

namespace Remorhaz\JSON\Pointer\Test\Locator\Reference;

use Remorhaz\JSON\Pointer\Locator\Reference;

class IsLastTest extends \PHPUnit_Framework_TestCase
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
