<?php

namespace Remorhaz\JSON\Pointer\Test\Pointer;

use Remorhaz\JSON\Data\RawSelectableReader;
use Remorhaz\JSON\Pointer\Pointer;

/**
 * @todo Merge tests for non-numeric indices.
 */
class TestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerExistingData
     */
    public function testExistingData(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        $result = (new Pointer($reader))->test($text);
        $this->assertTrue($result, "Error testing existing data");
    }


    public function providerExistingData(): array
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
    public function testTestNonExistingData(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        $result = (new Pointer($reader))->test($text);
        $this->assertFalse($result, "Error testing non-existing data");
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
            'nonExistingNotLastNestedProperty' => [
                '/a/b/c/d',
                (object) ['a' => (object) ['e' => (object) ['f' => 'g']]]
            ],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerInvalidData
     */
    public function testTestInvalidData(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        $result = (new Pointer($reader))->test($text);
        $this->assertFalse($result, "Error testing non-existing data");
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
     */
    public function testTestNonExistingNonAllowedNonNumericIndices(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        $result = (new Pointer($reader))->test($text);
        $this->assertFalse($result, "Error testing non-numeric array index");
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
     */
    public function testTestExistingNotAllowedNonNumericIndices(string $text, $data)
    {
        $reader = new RawSelectableReader($data);
        $result = (new Pointer($reader))->test($text);
        $this->assertFalse($result, "Error testing non-numeric array index");
    }


    public function providerExistingNonNumericIndicesData(): array
    {
        return [
            'rootNonNumericIndex' => ['/a', ['a' => 1]],
            'nestedNonNumericIndex' => ['/a/b', (object) ['a' => ['b' => 2]]],
        ];
    }
}
