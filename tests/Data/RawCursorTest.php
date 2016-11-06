<?php

namespace Remorhaz\JSON\Test\Data;

use Remorhaz\JSON\Data\RawCursor;

class RawCursorTest extends \PHPUnit_Framework_TestCase
{


    public function testBoundCursorPointsToDataRoot()
    {
        $data = (object) ['a' => 'b'];
        $cursor = (new RawCursor)->bind($data);
        $this->assertEquals($data, $cursor->getDataReference());
    }


    public function testNewCursorReportedNotBound()
    {
        $cursor = new RawCursor;
        $this->assertFalse($cursor->isBound());
    }


    public function testCursorReportedBoundAfterBinding()
    {
        $data = (object) ['a' => 'b'];
        $cursor = (new RawCursor)->bind($data);
        $this->assertTrue($cursor->isBound());
    }


    /**
     * @expectedException \Remorhaz\JSON\Data\Exception
     */
    public function testNewCursorThrowsExceptionOnGettingDataReference()
    {
        (new RawCursor)->getDataReference();
    }


    /**
     * @expectedException \LogicException
     */
    public function testNewCursorThrowsSplExceptionOnGettingDataReference()
    {
        (new RawCursor)->getDataReference();
    }


    /**
     * @expectedException \Remorhaz\JSON\Data\Exception
     */
    public function testNewCursorThrowsExceptionOnGettingDataCopy()
    {
        (new RawCursor)->getDataCopy();
    }


    /**
     * @expectedException \LogicException
     */
    public function testNewCursorThrowsSplExceptionOnGettingDataCopy()
    {
        (new RawCursor)->getDataCopy();
    }


    /**
     * @expectedException \Remorhaz\JSON\Data\Exception
     */
    public function testUnboundCursorThrowsExceptionOnGettingDataReference()
    {
        $data = (object) ['a' => 'b'];
        (new RawCursor)->bind($data)->unbind()->getDataReference();
    }


    /**
     * @expectedException \LogicException
     */
    public function testUnboundCursorThrowsSplExceptionOnGettingDataReference()
    {
        $data = (object) ['a' => 'b'];
        (new RawCursor)->bind($data)->unbind()->getDataReference();
    }


    /**
     * @expectedException \Remorhaz\JSON\Data\Exception
     */
    public function testUnboundCursorThrowsExceptionOnGettingDataCopy()
    {
        $data = (object) ['a' => 'b'];
        (new RawCursor)->bind($data)->unbind()->getDataCopy();
    }


    /**
     * @expectedException \LogicException
     */
    public function testUnboundCursorThrowsSplExceptionOnGettingDataCopy()
    {
        $data = (object) ['a' => 'b'];
        (new RawCursor)->bind($data)->unbind()->getDataCopy();
    }


    public function testCursorBindsByReference()
    {
        $data = (object) ['a' => 'b'];
        $cursor = (new RawCursor)->bind($data);
        $data->a = 'c';
        $expectedData = clone $data;
        $actualData = $cursor->getDataCopy();
        $this->assertEquals($expectedData, $actualData);
    }


    public function testNoReferenceOnObjectDataCopy()
    {
        $data = (object) ['a' => 'b'];
        $expectedData = clone $data;
        $cursor = (new RawCursor)->bind($data);
        $actualData = $cursor->getDataCopy();
        $data->a = 'c';
        $this->assertEquals($expectedData, $actualData);
    }


    public function testNoDataModificationAfterBinding()
    {
        $data = (object) ['a' => 'b'];
        $expectedData = clone $data;
        (new RawCursor)->bind($data->a);
        $this->assertEquals($expectedData, $data);
    }


    public function testNoDataModificationAfterUnbinding()
    {
        $data = (object) ['a' => 'b'];
        $expectedData = clone $data;
        (new RawCursor)->bind($data->a)->unbind();
        $this->assertEquals($expectedData, $data);
    }
}
