<?php

namespace Remorhaz\JSONPointer\Test\Pointer;

use Remorhaz\JSONPointer\Pointer;

class ReadTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $result
     * @param mixed $value
     * @param mixed $modifiedData
     * @dataProvider providerExistingData
     */
    public function testReadExistingDataByRef($text, $data, $result, $value, $modifiedData)
    {
        $pointer = Pointer::factory()
            ->setText($text)
            ->setData($data);
        $readData = &$pointer->read();
        $this->assertEquals($result, $readData, "Error reading existing data");
        $readData = $value;
        $this->assertEquals($modifiedData, $data, "Existing data was read not by reference");
    }


    public function providerExistingData()
    {
        return [
            'rootProperty' => [
                '/a',
                (object) ['a' => 1, 'b' => 2],
                1,
                3,
                (object) ['a' => 3, 'b' => 2],
            ],
            'nestedProperty' => [
                '/a/b',
                (object) ['a' => (object) ['b' => 1], 'c' => 2],
                1,
                3,
                (object) ['a' => (object) ['b' => 3], 'c' => 2],
            ],
            'rootIndex' => ['/1', [1, 2], 2, 3, [1, 3]],
            'nestedIndex' => ['/0/1', [[1, 2], 3], 2, 4, [[1, 4], 3]],
            'rootScalar' => ['', 'abc', 'abc', 1, 1],
            'rootNull' => ['', null, null, 'a', 'a'],
            'nestedNull' => [
                '/a',
                (object) ['a' => null],
                null,
                true,
                (object) ['a' => true],
            ],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerNonExistingData
     * @expectedException \Remorhaz\JSONPointer\EvaluatorException
     */
    public function testReadNonExistingDataThrowsEvaluatorException($text, $data)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->read();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerNonExistingData
     * @expectedException \RuntimeException
     */
    public function testReadNonExistingDataThrowsSplException($text, $data)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->read();
    }


    public function providerNonExistingData()
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
     * @expectedException \Remorhaz\JSONPointer\EvaluatorException
     */
    public function testReadInvalidDataThrowsEvaluatorException($text, $data)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->read();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerInvalidData
     * @expectedException \RuntimeException
     */
    public function testReadInvalidDataThrowsSplException($text, $data)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->read();
    }


    public function providerInvalidData()
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
     * @expectedException \Remorhaz\JSONPointer\EvaluatorException
     */
    public function testReadNonExistingNotAllowedNonNumericIndicesDataThrowsEvaluatorException($text, $data)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->read();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerNonExistingNonNumericIndicesData
     * @expectedException \RuntimeException
     */
    public function testReadNonExistingNotAllowedNonNumericIndicesDataThrowsSplException($text, $data)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->read();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerNonExistingNonNumericIndicesData
     * @expectedException \Remorhaz\JSONPointer\EvaluatorException
     */
    public function testReadNonExistingAllowedNonNumericIndicesDataThrowsEvaluatorException($text, $data)
    {
        Pointer::factory()
            ->setOptions(Pointer::OPTION_NON_NUMERIC_INDICES)
            ->setText($text)
            ->setData($data)
            ->read();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerNonExistingNonNumericIndicesData
     * @expectedException \RuntimeException
     */
    public function testReadNonExistingAllowedNonNumericIndicesDataThrowsSplException($text, $data)
    {
        Pointer::factory()
            ->setOptions(Pointer::OPTION_NON_NUMERIC_INDICES)
            ->setText($text)
            ->setData($data)
            ->read();
    }


    public function providerNonExistingNonNumericIndicesData()
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
     * @expectedException \Remorhaz\JSONPointer\EvaluatorException
     */
    public function testReadExistingNotAllowedNonNumericIndicesDataThrowsEvaluatorException($text, $data)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->read();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerExistingNonNumericIndicesData
     * @expectedException \RuntimeException
     */
    public function testReadExistingNotAllowedNonNumericIndicesDataThrowsSplException($text, $data)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->read();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $result
     * @param mixed $value
     * @param mixed $modifiedData
     * @dataProvider providerExistingNonNumericIndicesData
     */
    public function testReadExistingAllowedNonNumericIndicesData($text, $data, $result, $value, $modifiedData)
    {
        $pointer = Pointer::factory()
            ->setOptions(Pointer::OPTION_NON_NUMERIC_INDICES)
            ->setText($text)
            ->setData($data);
        $readData = &$pointer->read();
        $this->assertEquals($result, $readData, "Error reading non-numeric array index");
        $readData = $value;
        $this->assertEquals($modifiedData, $data, "Non-numeric data was read not by reference");
    }


    public function providerExistingNonNumericIndicesData()
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
