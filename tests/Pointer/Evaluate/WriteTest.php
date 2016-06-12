<?php

namespace Remorhaz\JSONPointer\Test\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator;
use Remorhaz\JSONPointer\Parser;
use Remorhaz\JSONPointer\Pointer\Evaluate\Write;

class WriteTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerSingleDataWithValidLocator
     * @expectedException \Remorhaz\JSONPointer\Pointer\Evaluate\Exception
     */
    public function testPerformWithNoValueSetThrowsException($text, $data)
    {
        $locator = Parser::factory()
            ->setText($text)
            ->getLocator();
        Write::factory()
            ->setData($data)
            ->setLocator($locator)
            ->perform();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerSingleDataWithValidLocator
     * @expectedException \LogicException
     */
    public function testPerformWithNoValueSetThrowsSplException($text, $data)
    {
        $locator = Parser::factory()
            ->setText($text)
            ->getLocator();
        Write::factory()
            ->setData($data)
            ->setLocator($locator)
            ->perform();
    }


    public function providerSingleDataWithValidLocator()
    {
        return [
            [
                '/a/b',
                (object) [
                    'a' => (object) ['b' => 1, 'c' => 2],
                ]
            ],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @param mixed $expectedData
     * @dataProvider providerDataWithValidLokator
     */
    public function testWriteDataWithValidLokator($text, $data, $value, $expectedData)
    {
        $locator = Parser::factory()
            ->setText($text)
            ->getLocator();
        Write::factory()
            ->setData($data)
            ->setLocator($locator)
            ->setValue($value)
            ->perform();
        $this->assertEquals($expectedData, $data, "Incorrect data after modification");
    }


    public function providerDataWithValidLokator()
    {
        return [
            'rootScalar' => ['', 1, 2, 2],
            'rootObjectExistingProperty' => [
                '/a',
                (object) ['a' => 1, 'b' => 2],
                3,
                (object) ['a' => 3, 'b' => 2],
            ],
            'rootObjectNewProperty' => [
                '/c',
                (object) ['a' => 1, 'b' => 2],
                3,
                (object) ['a' => 1, 'b' => 2, 'c' => 3],
            ],
            'nestedObjectExistingProperty' => [
                '/a/b',
                (object) [
                    'a' => (object) ['b' => 1, 'c' => 2],
                ],
                3,
                (object) [
                    'a' => (object) ['b' => 3, 'c' => 2],
                ],
            ],
            'nestedObjectNewProperty' => [
                '/a/d',
                (object) [
                    'a' => (object) ['b' => 1, 'c' => 2],
                ],
                3,
                (object) [
                    'a' => (object) ['b' => 1, 'c' => 2, 'd' => 3],
                ],
            ],
            'rootArrayExistingIndex' => [
                '/1',
                [1, 2, 3],
                4,
                [1, 4, 3],
            ],
            'rootArrayNextIndex' => [
                '/-',
                [1, 2, 3],
                4,
                [1, 2, 3, 4],
            ],
            'nestedArrayExistingIndex' => [
                '/1/0',
                [1, [3, 4], 5],
                6,
                [1, [6, 4], 5],
            ],
            'nestedArrayNextIndex' => [
                '/1/-',
                [1, [3, 4], 5],
                6,
                [1, [3, 4, 6], 5],
            ],
        ];
    }
}
