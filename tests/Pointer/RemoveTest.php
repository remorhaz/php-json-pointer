<?php

namespace Remorhaz\JSON\Pointer\Test\Pointer;

use Remorhaz\JSON\Data\RawSelectableWriter;
use Remorhaz\JSON\Pointer\Pointer;

class RemoveTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param string $text
     * @param $data
     * @param $expectedData
     * @dataProvider providerExistingData
     */
    public function testRemove_ExistingData_Removed(string $text, $data, $expectedData)
    {
        $writer = new RawSelectableWriter($data);
        (new Pointer($writer))->remove($text);
        $this->assertEquals($expectedData, $data);
    }


    /**
     * @todo Shorten dataset list.
     * @return array
     */
    public function providerExistingData(): array
    {
        return [
            'rootProperty' => [
                '/a',
                (object) ['a' => 1, 'b' => 2],
                (object) ['b' => 2],
            ],
            'rootNumericProperty' => [
                '/1',
                (object) [1 => 2, 'b' => 3],
                (object) ['b' => 3],
            ],
            'rootNegativeNumericProperty' => [
                '/-1',
                (object) [-1 => 2, 'b' => 3],
                (object) ['b' => 3],
            ],
            'nestedProperty' => [
                '/a/b',
                (object) ['a' => (object) ['b' => 1], 'c' => 2],
                (object) ['a' => (object) [], 'c' => 2],
            ],
            'rootIndex' => ['/1', [1, 2], [1]],
            'nestedIndex' => ['/0/1', [[1, 2], 3], [[1], 3]],
            'nestedNull' => [
                '/a',
                (object) ['a' => null],
                (object) [],
            ],
        ];
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessage Data root can't be removed
     */
    public function testRemove_LocatorPointsToWholeDocument_ExceptionThrown()
    {
        $data = (object) ['a' => 'b'];
        $writer = new RawSelectableWriter($data);
        (new Pointer($writer))->remove("");
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Data root can't be removed
     */
    public function testRemove_LocatorPointsToWholeDocument_SplExceptionThrown()
    {
        $data = (object) ['a' => 'b'];
        $writer = new RawSelectableWriter($data);
        (new Pointer($writer))->remove("");
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^Invalid index '-' at '(.*)'$/
     */
    public function testRemove_LocatorContainsNewIndex_ExceptionThrown()
    {
        $data = [[1, [2, [3]]]];
        $writer = new RawSelectableWriter($data);
        (new Pointer($writer))->remove("/0/1/-");
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Invalid index '-' at '(.*)'$/
     */
    public function testRemove_LocatorContainsNewIndex_SplExceptionThrown()
    {
        $data = [[1, [2, [3]]]];
        $writer = new RawSelectableWriter($data);
        (new Pointer($writer))->remove("/0/1/-");
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^No element #([1-9]\d*) at '(.*)'$/
     */
    public function testRemove_NonExistingElement_ExceptionThrown()
    {
        $data = [[1, [2, [3]]]];
        $writer = new RawSelectableWriter($data);
        (new Pointer($writer))->remove("/0/2");
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^No element #([1-9]\d*) at '(.*)'$/
     */
    public function testRemove_NonExistingElement_SplExceptionThrown()
    {
        $data = [[1, [2, [3]]]];
        $writer = new RawSelectableWriter($data);
        (new Pointer($writer))->remove("/0/2");
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^No property '(.*)' at '(.*)'$/
     */
    public function testRemove_NonExistingProperty_ExceptionThrown()
    {
        $data = (object) ['a' => 'b'];
        $writer = new RawSelectableWriter($data);
        (new Pointer($writer))->remove("/c");
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^No property '(.*)' at '(.*)'$/
     */
    public function testRemove_NonExistingProperty_SplExceptionThrown()
    {
        $data = (object) ['a' => 'b'];
        $writer = new RawSelectableWriter($data);
        (new Pointer($writer))->remove("/c");
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^Scalar data at '(.*)'$/
     */
    public function testRemove_LocatorContainsScalar_ExceptionThrown()
    {
        $data = (object) ['a' => 'b'];
        $writer = new RawSelectableWriter($data);
        (new Pointer($writer))->remove("/a/b");
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Scalar data at '(.*)'$/
     */
    public function testRemove_LocatorContainsScalar_SplExceptionThrown()
    {
        $data = (object) ['a' => 'b'];
        $writer = new RawSelectableWriter($data);
        (new Pointer($writer))->remove("/a/b");
    }
}
