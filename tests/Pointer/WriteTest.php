<?php

namespace Remorhaz\JSONPointer\Test\Pointer;

use Remorhaz\JSONPointer\Pointer;

class WriteTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @param mixed $modifiedData
     * @dataProvider providerExistingData
     */
    public function testWriteExistingData($text, $data, $value, $modifiedData)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->write($value);
        $this->assertEquals($modifiedData, $data, "Existing data was read not by reference");
    }


    public function providerExistingData()
    {
        return [
            'rootProperty' => [
                '/a',
                (object) ['a' => 1, 'b' => 2],
                3,
                (object) ['a' => 3, 'b' => 2],
            ],
            'nestedProperty' => [
                '/a/b',
                (object) ['a' => (object) ['b' => 1], 'c' => 2],
                3,
                (object) ['a' => (object) ['b' => 3], 'c' => 2],
            ],
            'rootIndex' => ['/1', [1, 2], 3, [1, 3]],
            'nestedIndex' => ['/0/1', [[1, 2], 3], 4, [[1, 4], 3]],
            'rootScalar' => ['', 'abc', 1, 1],
            'rootNull' => ['', null, 'a', 'a'],
            'nestedNull' => [
                '/a',
                (object) ['a' => null],
                true,
                (object) ['a' => true],
            ],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @param mixed $modifiedData
     * @dataProvider providerNonExistingData
     */
    public function testWriteNonExistingData($text, $data, $value, $modifiedData)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->write($value);
        $this->assertEquals($modifiedData, $data, "Existing data was read not by reference");
    }


    public function providerNonExistingData()
    {
        return [
            'nonExistingRootProperty' => [
                '/a',
                (object) ['c' => 1],
                2,
                (object) ['c' => 1, 'a' => 2],
            ],
            'nonExistingNestedProperty' => [
                '/a/b',
                (object) ['a' => (object) ['c' => 1]],
                2,
                (object) ['a' => (object) ['c' => 1, 'b' => 2]],
            ],
            'nonExistingRootNumericIndex' => ['/1', [1], 2, [1, 2]],
            'nonExistingNestedNumericIndex' => ['/0/1', [[1], 2], 3, [[1, 3], 2]],
            'rootNextIndex' => ['/-', [1, 2], 3, [1, 2, 3]],
            'nestedNextIndex' => [
                '/a/-',
                (object) ['a' => [1, 2]],
                3,
                (object) ['a' => [1, 2, 3]],
            ],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerInvalidData
     * @expectedException \Remorhaz\JSONPointer\EvaluatorException
     */
    public function testWriteInvalidDataThrowsEvaluatorException($text, $data)
    {
        $value = 1;
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->write($value);
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
     * @param mixed $value
     * @dataProvider providerNonExistingNonNumericIndicesData
     * @expectedException \Remorhaz\JSONPointer\EvaluatorException
     */
    public function testWriteNonExistingNotAllowedNonNumericIndicesDataThrowsEvaluatorException($text, $data, $value)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->write($value);
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @dataProvider providerNonExistingNonNumericIndicesData
     * @expectedException \RuntimeException
     */
    public function testWriteNonExistingNotAllowedNonNumericIndicesDataThrowsSplException($text, $data, $value)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->write($value);
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @param mixed $modifiedData
     * @dataProvider providerNonExistingNonNumericIndicesData
     */
    public function testWriteNonExistingAllowedNonNumericIndicesData($text, $data, $value, $modifiedData)
    {
        Pointer::factory()
            ->setOptions(Pointer::OPTION_NON_NUMERIC_INDICES)
            ->setText($text)
            ->setData($data)
            ->write($value);
        $this->assertEquals($modifiedData, $data, "Existing data was read not by reference");
    }


    public function providerNonExistingNonNumericIndicesData()
    {
        return [
            'nonExistingRootNonNumericIndex' => ['/a', [1], 2, [1, 'a' => 2]],
            'nonExistingNestedNonNumericIndex' => ['/0/a', [[1], 2], 3, [[1, 'a' => 3], 2]],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @dataProvider providerExistingNonNumericIndicesData
     * @expectedException \Remorhaz\JSONPointer\EvaluatorException
     */
    public function testWriteExistingNotAllowedNonNumericIndicesDataThrowsEvaluatorException($text, $data, $value)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->write($value);
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @dataProvider providerExistingNonNumericIndicesData
     * @expectedException \RuntimeException
     */
    public function testWriteExistingNotAllowedNonNumericIndicesDataThrowsSplException($text, $data, $value)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->write($value);
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @param mixed $modifiedData
     * @dataProvider providerExistingNonNumericIndicesData
     */
    public function testWriteExistingAllowedNonNumericIndicesData($text, $data, $value, $modifiedData)
    {
        Pointer::factory()
            ->setOptions(Pointer::OPTION_NON_NUMERIC_INDICES)
            ->setText($text)
            ->setData($data)
            ->write($value);
        $this->assertEquals($modifiedData, $data, "Existing data was read not by reference");
    }


    public function providerExistingNonNumericIndicesData()
    {
        return [
            'existingRootNonNumericIndex' => ['/a', [1, 'a' => 2], 3, [1, 'a' => 3]],
            'existingNestedNonNumericIndex' => ['/0/a', [['a' => 1], 2], 3, [['a' => 3], 2]],
        ];
    }
}