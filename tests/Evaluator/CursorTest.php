<?php

namespace Remorhaz\JSONPointer\Test\Evaluator;

use Remorhaz\JSONPointer\Evaluator\Cursor;
use Remorhaz\JSONPointer\Locator\Reference;

class CursorTest extends \PHPUnit_Framework_TestCase
{


    public function testGotReferenceSameAsSet()
    {
        $reference = Reference::factory();
        $gotReference = Cursor::factory()
            ->setReference($reference)
            ->getReference();
        $this->assertSame($reference, $gotReference, "Got reference differs from the ona that was set");
    }


    /**
     * @expectedException \Remorhaz\JSONPointer\Evaluator\Exception
     */
    public function testUninitializedReferenceAccessThrowsException()
    {
        Cursor::factory()->getReference();
    }


    /**
     * @expectedException \LogicException
     */
    public function testUninitializedReferenceAccessThrowsSplException()
    {
        Cursor::factory()->getReference();
    }


    /**
     * @param mixed $setData
     * @param mixed $replaceData
     * @dataProvider providerData
     */
    public function testGotDataSameAsSet($setData, $replaceData)
    {
        $gotData = &Cursor::factory()
            ->setData($setData)
            ->getData();
        $this->assertSame($setData, $gotData, "Got data differs from the one that was set");
        $gotData = $replaceData;
        $this->assertSame($replaceData, $setData, "Got data is not passed by reference");
    }


    public function providerData()
    {
        return [
            'null' => [null, 1],
            'scalar' => [1, 2],
            'array' => [[1, 2, '3'], [1, 2, 3]],
            'object' => [(object) ['a', 'b', 'c'], (object) ['a', 'b']],
        ];
    }


    /**
     * @expectedException \Remorhaz\JSONPointer\Evaluator\Exception
     */
    public function testUninitializedDataAccessThrowsException()
    {
        Cursor::factory()->getData();
    }


    /**
     * @expectedException \LogicException
     */
    public function testUninitializedDataAccessThrowsSplException()
    {
        Cursor::factory()->getData();
    }
}
