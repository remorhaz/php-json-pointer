<?php

namespace Remorhaz\JSON\Pointer\Test\Pointer;

use Remorhaz\JSON\Data\Reference\Reader;
use Remorhaz\JSON\Data\Reference\Writer;
use Remorhaz\JSON\Pointer\Pointer;

class ReplaceTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param mixed $data
     * @param string $text
     * @param mixed $value
     * @param $expectedData
     * @dataProvider providerValueExists
     */
    public function testReplace_ValueExists_DataReplaced($data, string $text, $value, $expectedData)
    {
        $writer = new Writer($data);
        $valueReader = new Reader($value);
        (new Pointer($writer))->replace($text, $valueReader);
        $this->assertEquals($expectedData, $data, "Existing value was not replaced");
    }


    public function providerValueExists(): array
    {
        return [
            'element' => [[1], "/0", 2, [2]],
            'property' => [(object) ['a' => 'b'], "/a", 'c', (object) ['a' => 'c']],
            'root' => ['a', "", 'b', 'b'],
        ];
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^No element #([1-9]\d*) at '(.*)'$/
     */
    public function testReplace_ElementNotExists_ExceptionThrown()
    {
        $data = [1];
        $writer = new Writer($data);
        $value = 2;
        $valueReader = new Reader($value);
        (new Pointer($writer))->replace("/1", $valueReader);
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^No element #([1-9]\d*) at '(.*)'$/
     */
    public function testReplace_ElementNotExists_SplExceptionThrown()
    {
        $data = [1];
        $writer = new Writer($data);
        $value = 2;
        $valueReader = new Reader($value);
        (new Pointer($writer))->replace("/1", $valueReader);
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^No property '(.*)' at '(.*)'$/
     */
    public function testReplace_PropertyNotExists_ExceptionThrown()
    {
        $data = (object) ['a' => 'b'];
        $writer = new Writer($data);
        $value = 'd';
        $valueReader = new Reader($value);
        (new Pointer($writer))->replace("/c", $valueReader);
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^No property '(.*)' at '(.*)'$/
     */
    public function testReplace_PropertyNotExists_SplExceptionThrown()
    {
        $data = (object) ['a' => 'b'];
        $writer = new Writer($data);
        $value = 'd';
        $valueReader = new Reader($value);
        (new Pointer($writer))->replace("/c", $valueReader);
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^Scalar data at '(.*)'$/
     */
    public function testReplace_ScalarSelection_ExceptionThrown()
    {
        $data = 'a';
        $writer = new Writer($data);
        $value = 'd';
        $valueReader = new Reader($value);
        (new Pointer($writer))->replace("/a", $valueReader);
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Scalar data at '(.*)'$/
     */
    public function testReplace_ScalarSelection_SplExceptionThrown()
    {
        $data = 'a';
        $writer = new Writer($data);
        $value = 'd';
        $valueReader = new Reader($value);
        (new Pointer($writer))->replace("/a", $valueReader);
    }


    /**
     * @param mixed $data
     * @param string $text
     * @param mixed $value
     * @dataProvider providerInvalidElementIndex
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^Invalid index '(.*)' at '(.*)'$/
     */
    public function testReplace_InvalidElementIndex_ExceptionThrown($data, string $text, $value)
    {
        $writer = new Writer($data);
        $valueReader = new Reader($value);
        (new Pointer($writer))->replace($text, $valueReader);
    }


    /**
     * @param mixed $data
     * @param string $text
     * @param mixed $value
     * @dataProvider providerInvalidElementIndex
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Invalid index '(.*)' at '(.*)'$/
     */
    public function testReplace_InvalidElementIndex_SplExceptionThrown($data, string $text, $value)
    {
        $writer = new Writer($data);
        $valueReader = new Reader($value);
        (new Pointer($writer))->replace($text, $valueReader);
    }


    public function providerInvalidElementIndex(): array
    {
        return [
            'propertyReference' => [[1], "/a", 2],
            'newIndexReference' => [[1], "/-", 2],
        ];
    }
}
