<?php

namespace Remorhaz\JSON\Pointer\Test\Pointer;

use Remorhaz\JSON\Pointer\Pointer;

class AddTest extends \PHPUnit_Framework_TestCase
{


    public function testAdd_PropertyExists_Replaced()
    {
        $data = (object) ['a' => 'b'];
        Pointer::factory()
            ->setData($data)
            ->setText("/a")
            ->add('c');
        $expectedData = (object) ['a' => 'c'];
        $this->assertEquals($expectedData, $data);
    }


    public function testAdd_PropertyNotExists_Inserted()
    {
        $data = (object) ['a' => 'b'];
        Pointer::factory()
            ->setData($data)
            ->setText("/c")
            ->add('d');
        $expectedData = (object) ['a' => 'b', 'c' => 'd'];
        $this->assertEquals($expectedData, $data);
    }


    public function testAdd_ElementExists_Inserted()
    {
        $data = [1, 3];
        Pointer::factory()
            ->setData($data)
            ->setText("/1")
            ->add(2);
        $expectedData = [1, 2, 3];
        $this->assertEquals($expectedData, $data);
    }


    public function testAdd_NextElementNotExists_Appended()
    {
        $data = [1, 2];
        Pointer::factory()
            ->setData($data)
            ->setText("/2")
            ->add(3);
        $expectedData = [1, 2, 3];
        $this->assertEquals($expectedData, $data);
    }


    public function testAdd_NewElement_Appended()
    {
        $data = [1, 2];
        Pointer::factory()
            ->setData($data)
            ->setText("/-")
            ->add(3);
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
        Pointer::factory()
            ->setData($data)
            ->setText("/3")
            ->add(3);
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Invalid index #([1-9]\d*) at '(.*)'$/
     */
    public function testAdd_NotNextElementNotExists_SplExceptionThrown()
    {
        $data = [1, 2];
        Pointer::factory()
            ->setData($data)
            ->setText("/3")
            ->add(3);
    }


    /**
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^Invalid index '(.*)' at '(.*)'$/
     */
    public function testAdd_IvalidElementIndex_ExceptionThrown()
    {
        $data = [1, 2];
        Pointer::factory()
            ->setData($data)
            ->setText("/a")
            ->add(3);
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^Invalid index '(.*)' at '(.*)'$/
     */
    public function testAdd_IvalidElementIndex_SplExceptionThrown()
    {
        $data = [1, 2];
        Pointer::factory()
            ->setData($data)
            ->setText("/a")
            ->add(3);
    }


    /**
     * @param $data
     * @param string $text
     * @param mixed $newValue
     * @dataProvider providerNonExistingSelection
     * @expectedException \Remorhaz\JSON\Pointer\EvaluatorException
     * @expectedExceptionMessageRegExp /^No data at '(.*)'$/
     */
    public function testAdd_NonExistingSelection_ExceptionThrown($data, string $text, $newValue)
    {
        Pointer::factory()
            ->setData($data)
            ->setText($text)
            ->add($newValue);
    }


    /**
     * @param $data
     * @param string $text
     * @param mixed $newValue
     * @dataProvider providerNonExistingSelection
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /^No data at '(.*)'$/
     */
    public function testAdd_NonExistingSelection_SplExceptionThrown($data, string $text, $newValue)
    {
        Pointer::factory()
            ->setData($data)
            ->setText($text)
            ->add($newValue);
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
        Pointer::factory()
            ->setData($data)
            ->setText("/a")
            ->add('b');
    }
}
