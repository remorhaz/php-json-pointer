<?php

namespace Remorhaz\JSON\Pointer\Test\Pointer;

use Remorhaz\JSON\Data\Reference\Reader;
use Remorhaz\JSON\Data\Reference\Writer;
use Remorhaz\JSON\Pointer\Pointer;

class AddTest extends \PHPUnit_Framework_TestCase
{


    public function testAdd_PropertyExists_Replaced()
    {
        $data = (object) ['a' => 'b'];
        $writer = new Writer($data);
        $value = 'c';
        $valueReader = new Reader($value);
        (new Pointer($writer))->add("/a", $valueReader);
        $expectedData = (object) ['a' => 'c'];
        $this->assertEquals($expectedData, $data);
    }


    public function testAdd_PropertyNotExists_Inserted()
    {
        $data = (object) ['a' => 'b'];
        $writer = new Writer($data);
        $value = 'd';
        $valueReader = new Reader($value);
        (new Pointer($writer))->add("/c", $valueReader);
        $expectedData = (object) ['a' => 'b', 'c' => 'd'];
        $this->assertEquals($expectedData, $data);
    }


    public function testAdd_ElementExists_Inserted()
    {
        $data = [1, 3];
        $writer = new Writer($data);
        $value = 2;
        $valueReader = new Reader($value);
        (new Pointer($writer))->add("/1", $valueReader);
        $expectedData = [1, 2, 3];
        $this->assertEquals($expectedData, $data);
    }


    public function testAdd_NextElementNotExists_Appended()
    {
        $data = [1, 2];
        $writer = new Writer($data);
        $value = 3;
        $valueReader = new Reader($value);
        (new Pointer($writer))->add("/2", $valueReader);
        $expectedData = [1, 2, 3];
        $this->assertEquals($expectedData, $data);
    }


    public function testAdd_NewElement_Appended()
    {
        $data = [1, 2];
        $writer = new Writer($data);
        $value = 3;
        $valueReader = new Reader($value);
        (new Pointer($writer))->add("/-", $valueReader);
        $expectedData = [1, 2, 3];
        $this->assertEquals($expectedData, $data);
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^Invalid index #([1-9]\d*) at '(.*)'$/
     */
    public function testAdd_NotNextElementNotExists_ExceptionThrown()
    {
        $data = [1, 2];
        $writer = new Writer($data);
        $value = 3;
        $valueReader = new Reader($value);
        (new Pointer($writer))->add("/3", $valueReader);
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Invalid index #([1-9]\d*) at '(.*)'$/
     */
    public function testAdd_NotNextElementNotExists_SplExceptionThrown()
    {
        $data = [1, 2];
        $writer = new Writer($data);
        $value = 3;
        $valueReader = new Reader($value);
        (new Pointer($writer))->add("/3", $valueReader);
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^Invalid index '(.*)' at '(.*)'$/
     */
    public function testAdd_IvalidElementIndex_ExceptionThrown()
    {
        $data = [1, 2];
        $writer = new Writer($data);
        $value = 3;
        $valueReader = new Reader($value);
        (new Pointer($writer))->add("/a", $valueReader);
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Invalid index '(.*)' at '(.*)'$/
     */
    public function testAdd_IvalidElementIndex_SplExceptionThrown()
    {
        $data = [1, 2];
        $writer = new Writer($data);
        $value = 3;
        $valueReader = new Reader($value);
        (new Pointer($writer))->add("/a", $valueReader);
    }


    /**
     * @param $data
     * @param string $text
     * @param mixed $value
     * @dataProvider providerNonExistingSelection
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^No data at '(.*)'$/
     */
    public function testAdd_NonExistingSelection_ExceptionThrown($data, string $text, $value)
    {
        $writer = new Writer($data);
        $valueReader = new Reader($value);
        (new Pointer($writer))->add($text, $valueReader);
    }


    /**
     * @param $data
     * @param string $text
     * @param mixed $value
     * @dataProvider providerNonExistingSelection
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^No data at '(.*)'$/
     */
    public function testAdd_NonExistingSelection_SplExceptionThrown($data, string $text, $value)
    {
        $writer = new Writer($data);
        $valueReader = new Reader($value);
        (new Pointer($writer))->add($text, $valueReader);
    }


    public function providerNonExistingSelection(): array
    {
        return [
            'nonExistingIndex' => [[1, 2], "/2/0", 4],
            'newIndex' => [[1, 2], "/-/0", 4],
            'nonExistingProperty' => [(object) ['a' => 'b'], "/c/d", 'e'],
        ];
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^Scalar data at '(.*)'$/
     */
    public function testAdd_ScalarSelection_ExceptionThrown()
    {
        $data = 'a';
        $writer = new Writer($data);
        $value = 'b';
        $valueReader = new Reader($value);
        (new Pointer($writer))->add("/a", $valueReader);
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Scalar data at '(.*)'$/
     */
    public function testAdd_ScalarSelection_SplExceptionThrown()
    {
        $data = 'a';
        $writer = new Writer($data);
        $value = 'b';
        $valueReader = new Reader($value);
        (new Pointer($writer))->add("/a", $valueReader);
    }
}
