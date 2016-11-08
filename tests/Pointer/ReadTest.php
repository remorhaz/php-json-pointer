<?php

namespace Remorhaz\JSON\Pointer\Test\Pointer;

use Remorhaz\JSON\Data\RawSelectableReader;
use Remorhaz\JSON\Pointer\Pointer;

/**
 * @todo Merge tests for non-numeric indices.
 */
class ReadTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $result
     * @dataProvider providerExistingData
     */
    public function testReadExistingData(string $text, $data, $result)
    {
        $reader = new RawSelectableReader($data);
        $readData = (new Pointer($reader))->read($text)->getData();
        $this->assertEquals($result, $readData, "Error reading existing data");
    }


    public function providerExistingData(): array
    {
        return [
            'rootProperty' => ['/a', (object) ['a' => 1, 'b' => 2], 1],
            'rootNumericProperty' => ['/1', (object) [1 => 2, 'b' => 3], 2],
            'rootNegativeNumericProperty' => ['/-1', (object) [-1 => 2, 'b' => 3], 2],
            'nestedProperty' => ['/a/b', (object) ['a' => (object) ['b' => 1], 'c' => 2], 1],
            'rootIndex' => ['/1', [1, 2], 2],
            'nestedIndex' => ['/0/1', [[1, 2], 3], 2],
            'rootScalar' => ['', 'abc', 'abc'],
            'rootNull' => ['', null, null],
            'nestedNull' => ['/a', (object) ['a' => null], null],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerNonExistingData
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     */
    public function testReadNonExistingDataThrowsEvaluatorException(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        (new Pointer($reader))->read($text);
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerNonExistingData
     * @expectedException \RuntimeException
     */
    public function testReadNonExistingDataThrowsSplException(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        (new Pointer($reader))->read($text);
    }


    public function providerNonExistingData(): array
    {
        return [
            'nonExistingRootProperty' => ['/a', (object) ['b' => 1]],
            'nonExistingNestedProperty' => ['/a/b', (object) ['a' => (object) ['c' => 1]]],
            'nonExistingRootNumericIndex' => ['/1', [1]],
            'nonExistingNestedNumericIndex' => ['/0/1', [[1], 2]],
            'rootNextIndex' => ['/-', [1, 2]],
            'nestedNextIndex' => ['/a/-', (object) ['a' => [1, 2]]],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerInvalidData
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     */
    public function testReadInvalidDataThrowsEvaluatorException(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        (new Pointer($reader))->read($text);
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerInvalidData
     * @expectedException \RuntimeException
     */
    public function testReadInvalidDataThrowsSplException(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        (new Pointer($reader))->read($text);
    }


    public function providerInvalidData(): array
    {
        return [
            'rootArrayProperty' => ['/a', []],
            'nestedArrayProperty' => ['/a/b', (object) ['a' => []]],
            'rootScalarKey' => ['/a', 1],
            'nestedScalarKey' => ['/a/b', (object) ['a' => 1]],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerNonExistingNonNumericIndicesData
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     */
    public function testReadNonExistingNotAllowedNonNumericIndicesDataThrowsEvaluatorException(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        (new Pointer($reader))->read($text);
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerNonExistingNonNumericIndicesData
     * @expectedException \RuntimeException
     */
    public function testReadNonExistingNotAllowedNonNumericIndicesDataThrowsSplException(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        (new Pointer($reader))->read($text);
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerNonExistingNonNumericIndicesData
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     */
    public function testReadNonExistingAllowedNonNumericIndicesDataThrowsEvaluatorException(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        (new Pointer($reader))->read($text);
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerNonExistingNonNumericIndicesData
     * @expectedException \RuntimeException
     */
    public function testReadNonExistingAllowedNonNumericIndicesDataThrowsSplException(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        (new Pointer($reader))->read($text);
    }


    public function providerNonExistingNonNumericIndicesData(): array
    {
        return [
            'nonExistingRootNonNumericIndex' => ['/a', [1]],
            'nonExistingNestedNonNumericIndex' => ['/0/a', [[1], 2]],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerExistingNonNumericIndicesData
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     */
    public function testReadExistingNotAllowedNonNumericIndicesDataThrowsEvaluatorException(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        (new Pointer($reader))->read($text);
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerExistingNonNumericIndicesData
     * @expectedException \RuntimeException
     */
    public function testReadExistingNotAllowedNonNumericIndicesDataThrowsSplException(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        (new Pointer($reader))->read($text);
    }


    public function providerExistingNonNumericIndicesData(): array
    {
        return [
            'rootNonNumericIndex' => ["/a", ['a' => 1], 1, 2, ['a' => 2]],
            'nestedNonNumericIndex' => [
                "/a/b",
                (object) ['a' => ['b' => 2]],
                2,
                3,
                (object) ['a' => ['b' => 3]],
            ],
        ];
    }
}
