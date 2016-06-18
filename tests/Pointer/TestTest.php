<?php

namespace Remorhaz\JSONPointer\Test\Pointer;

use Remorhaz\JSONPointer\Pointer;

class TestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerExistingData
     */
    public function testExistingData($text, $data)
    {
        $pointer = Pointer::factory()
            ->setText($text)
            ->setData($data);
        $this->assertTrue($pointer->test(), "Error testing existing data");
    }


    public function providerExistingData()
    {
        return [
            'rootProperty' => ['/a', (object) ['a' => 1, 'b' => 2]],
            'nestedProperty' => [
                '/a/b',
                (object) ['a' => (object) ['b' => 1], 'c' => 2],
            ],
            'rootIndex' => ['/1', [1, 2]],
            'nestedIndex' => ['/0/1', [[1, 2], 3]],
            'rootScalar' => ['', 'abc'],
            'rootNull' => ['', null],
            'nestedNull' => ['/a', (object) ['a' => null]],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerNonExistingData
     */
    public function testTestNonExistingData($text, $data)
    {
        $result = Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->test();
        $this->assertFalse($result, "Error testing non-existing data");
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
     */
    public function testTestInvalidData($text, $data)
    {
        $result = Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->test();
        $this->assertFalse($result, "Error testing non-existing data");
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
     */
    public function testTestNonExistingNonAllowedNonNumericIndices($text, $data)
    {
        $result = Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->test();
        $this->assertFalse($result, "Error testing non-numeric array index");
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerNonExistingNonNumericIndicesData
     */
    public function testTestNonExistingAllowedNonNumericIndices($text, $data)
    {
        $result = Pointer::factory()
            ->setOptions(Pointer::OPTION_NON_NUMERIC_INDICES)
            ->setText($text)
            ->setData($data)
            ->test();
        $this->assertFalse($result, "Error testing non-numeric array index");
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
     */
    public function testTestExistingAllowedNonNumericIndices($text, $data)
    {
        $result = Pointer::factory()
            ->setOptions(Pointer::OPTION_NON_NUMERIC_INDICES)
            ->setText($text)
            ->setData($data)
            ->test();
        $this->assertTrue($result, "Error testing non-numeric array index");
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerExistingNonNumericIndicesData
     */
    public function testTestExistingNotAllowedNonNumericIndices($text, $data)
    {
        $result = Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->test();
        $this->assertFalse($result, "Error testing non-numeric array index");
    }


    public function providerExistingNonNumericIndicesData()
    {
        return [
            'rootNonNumericIndex' => ['/a', ['a' => 1]],
            'nestedNonNumericIndex' => ['/a/b', (object) ['a' => ['b' => 2]]],
        ];
    }
}
