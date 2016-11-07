<?php

namespace Remorhaz\JSON\Pointer\Test\Pointer;

use Remorhaz\JSON\Pointer\Pointer;

class DeleteTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param string $text
     * @param $data
     * @param $expectedData
     * @dataProvider providerExistingData
     */
    public function testDeleteExistingData(string $text, $data, $expectedData)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->delete();
        $this->assertEquals($expectedData, $data);
    }


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
     * @expectedExceptionMessage Data root can't be deleted
     */
    public function testDelete_LocatorPointsToWholeDocument_ExceptionThrown()
    {
        $data = (object) ['a' => 'b'];
        Pointer::factory()
            ->setText("")
            ->setData($data)
            ->delete();
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Data root can't be deleted
     */
    public function testDelete_LocatorPointsToWholeDocument_SplExceptionThrown()
    {
        $data = (object) ['a' => 'b'];
        Pointer::factory()
            ->setText("")
            ->setData($data)
            ->delete();
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^Invalid index '-' at '(.*)'$/
     */
    public function testDelete_LocatorContainsNewIndex_ExceptionThrown()
    {
        $data = [[1, [2, [3]]]];
        Pointer::factory()
            ->setText("/0/1/-")
            ->setData($data)
            ->delete();
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Invalid index '-' at '(.*)'$/
     */
    public function testDelete_LocatorContainsNewIndex_SplExceptionThrown()
    {
        $data = [[1, [2, [3]]]];
        Pointer::factory()
            ->setText("/0/1/-")
            ->setData($data)
            ->delete();
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^No element #([1-9]\d*) at '(.*)'$/
     */
    public function testDelete_NonExistingElement_ExceptionThrown()
    {
        $data = [[1, [2, [3]]]];
        Pointer::factory()
            ->setText("/0/2")
            ->setData($data)
            ->delete();
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^No element #([1-9]\d*) at '(.*)'$/
     */
    public function testDelete_NonExistingElement_SplExceptionThrown()
    {
        $data = [[1, [2, [3]]]];
        Pointer::factory()
            ->setText("/0/2")
            ->setData($data)
            ->delete();
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^No property '(.*)' at '(.*)'$/
     */
    public function testDelete_NonExistingProperty_ExceptionThrown()
    {
        $data = (object) ['a' => 'b'];
        Pointer::factory()
            ->setText("/c")
            ->setData($data)
            ->delete();
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^No property '(.*)' at '(.*)'$/
     */
    public function testDelete_NonExistingProperty_SplExceptionThrown()
    {
        $data = (object) ['a' => 'b'];
        Pointer::factory()
            ->setText("/c")
            ->setData($data)
            ->delete();
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^Scalar data at '(.*)'$/
     */
    public function testDelete_LocatorContainsScalar_ExceptionThrown()
    {
        $data = (object) ['a' => 'b'];
        Pointer::factory()
            ->setText("/a/b")
            ->setData($data)
            ->delete();
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Scalar data at '(.*)'$/
     */
    public function testDelete_LocatorContainsScalar_SplExceptionThrown()
    {
        $data = (object) ['a' => 'b'];
        Pointer::factory()
            ->setText("/a/b")
            ->setData($data)
            ->delete();
    }
}
