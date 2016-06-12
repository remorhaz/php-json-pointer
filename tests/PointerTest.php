<?php

namespace Remorhaz\JSONPointer\Test;

use Remorhaz\JSONPointer\Pointer;

class PointerTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerReadDataWithValidLocator
     */
    public function testTestDataWithValidLocator($text, $data)
    {
        $pointer = Pointer::factory()
            ->setText($text)
            ->setData($data);
        $this->assertTrue($pointer->test(), "Error testing existing data");
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @dataProvider providerReadDataWithValidLocator
     */
    public function testReadDataWithValidLocator($text, $data, $value)
    {
        $pointer = Pointer::factory()
            ->setText($text)
            ->setData($data);
        $this->assertEquals($value, $pointer->read(), "Error reading data");
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @param mixed $newValue
     * @param mixed $newData
     * @dataProvider providerReadDataWithValidLocator
     */
    public function testReadDataByRefWithValidLocator($text, $data, $value, $newValue, $newData)
    {
        $pointer = Pointer::factory()
            ->setText($text)
            ->setData($data);
        $valueRef = &$pointer->read();
        $this->assertEquals($value, $valueRef, "Error reading data by reference");
        $valueRef = $newValue;
        $this->assertEquals($newData, $data, "Incorrect data after modifying by read value reference");
    }


    public function providerReadDataWithValidLocator()
    {
        return [
            'objectProperty' => [
                '/a',
                (object) ['a' => 1, 'b' => 2],
                1,
                3,
                (object) ['a' => 3, 'b' => 2],
            ],
            'nestedObjectProperty' => [
                '/a/b',
                (object) ['a' => (object) ['b' => 1], 'c' => 2],
                1,
                3,
                (object) ['a' => (object) ['b' => 3], 'c' => 2],
            ],
            'null' => ['', null, null, false, false],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $newValue
     * @param mixed $newData
     * @dataProvider providerWriteDataWithValidLocator
     */
    public function testWriteDataWithValidLocator($text, $data, $newValue, $newData)
    {
        $pointer = Pointer::factory()
            ->setText($text)
            ->setData($data);
        $pointer->write($newValue);
        $this->assertEquals($newData, $data, "Incorrect data after writing");
    }


    public function providerWriteDataWithValidLocator()
    {
        return [
            'rootProperty' => [
                '/a',
                (object) ['a' => 1, 'b' => 2],
                3,
                (object) ['a' => 3, 'b' => 2],
            ],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerReadDataWithInvalidLocator
     */
    public function testTestDataWithInvalidLocator($text, $data)
    {
        $result = Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->test();
        $this->assertFalse($result, "Error testing non-existing data");
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerReadDataWithInvalidLocator
     * @expectedException \Remorhaz\JSONPointer\EvaluateException
     */
    public function testReadDataWithInvalidLocatorThrowsException($text, $data)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->read();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerReadDataWithInvalidLocator
     * @expectedException \RuntimeException
     */
    public function testReadDataWithInvalidLocatorThrowsSplException($text, $data)
    {
        Pointer::factory()
            ->setText($text)
            ->setData($data)
            ->read();
    }


    public function providerReadDataWithInvalidLocator()
    {
        return [
            'nonExistingRootProperty' => ["/a/b", (object) ['c' => 1]],
            'nonExistingNestedProperty' => ["/a/b", (object) ['a' => (object) ['c' => 1]]],
            'rootArrayProperty' => ["/a", []],
            'nestedArrayProperty' => ["/a/b", (object) ['a' => []]],
            'rootArrayNextIndex' => ["/-", []],
            'nesterArrayNextIndex' => ["/a/-", (object) ['a' => []]],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerReadDataWithNonNumericArrayIndices
     */
    public function testTestDataWithNonNumericArrayIndices($text, $data)
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
     * @param mixed $result
     * @dataProvider providerReadDataWithNonNumericArrayIndices
     */
    public function testReadDataWithNonNumericArrayIndices($text, $data, $result)
    {
        $pointer = Pointer::factory()
            ->setOptions(Pointer::OPTION_NON_NUMERIC_INDICES)
            ->setText($text)
            ->setData($data);
        $this->assertEquals($result, $pointer->read(), "Error reading non-numeric array index");
    }


    public function providerReadDataWithNonNumericArrayIndices()
    {
        return [
            'rootArrayProperty' => ["/a", ['a' => 1], 1],
            'nestedArrayProperty' => ["/a/b", (object) ['a' => ['b' => 2]], 2],
        ];
    }
}
