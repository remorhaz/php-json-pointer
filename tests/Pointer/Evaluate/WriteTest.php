<?php

namespace Remorhaz\JSONPointer\Test\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator;
use Remorhaz\JSONPointer\Parser;
use Remorhaz\JSONPointer\Pointer\Evaluate\LocatorWrite;

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
        LocatorWrite::factory()
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
        LocatorWrite::factory()
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
     * @dataProvider providerDataWithValidLocator
     */
    public function testWriteDataWithValidLocator($text, $data, $value, $expectedData)
    {
        $locator = Parser::factory()
            ->setText($text)
            ->getLocator();
        LocatorWrite::factory()
            ->setData($data)
            ->setLocator($locator)
            ->setValue($value)
            ->perform();
        $this->assertEquals($expectedData, $data, "Incorrect data after writing");
    }


    public function providerDataWithValidLocator()
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
            'rootArrayNewIndex' => [
                '/3',
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
            'nestedArrayNewIndex' => [
                '/1/2',
                [1, [3, 4], 5],
                6,
                [1, [3, 4, 6], 5],
            ],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @dataProvider providerDataWithNonNumericIndexLocator
     * @expectedException \Remorhaz\JSONPointer\Pointer\Evaluate\Exception
     */
    public function testAccessNonNumericIndexThrowsException($text, $data, $value)
    {
        $locator = Parser::factory()
            ->setText($text)
            ->getLocator();
        LocatorWrite::factory()
            ->setData($data)
            ->setLocator($locator)
            ->setValue($value)
            ->perform();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @dataProvider providerDataWithNonNumericIndexLocator
     * @expectedException \Remorhaz\JSONPointer\EvaluateException
     */
    public function testAccessNonNumericIndexThrowsEvaluateException($text, $data, $value)
    {
        $locator = Parser::factory()
            ->setText($text)
            ->getLocator();
        LocatorWrite::factory()
            ->setData($data)
            ->setLocator($locator)
            ->setValue($value)
            ->perform();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @dataProvider providerDataWithNonNumericIndexLocator
     * @expectedException \RuntimeException
     */
    public function testAccessNonNumericIndexThrowsSplException($text, $data, $value)
    {
        $locator = Parser::factory()
            ->setText($text)
            ->getLocator();
        LocatorWrite::factory()
            ->setData($data)
            ->setLocator($locator)
            ->setValue($value)
            ->perform();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @param mixed $expectedData
     * @dataProvider providerDataWithNonNumericIndexLocator
     */
    public function testWriteAllowedNonNumericIndex($text, $data, $value, $expectedData)
    {
        $locator = Parser::factory()
            ->setText($text)
            ->getLocator();
        LocatorWrite::factory()
            ->setData($data)
            ->setLocator($locator)
            ->setValue($value)
            ->allowNonNumericIndices()
            ->perform();
        $this->assertEquals($expectedData, $data, "Incorrect data after writing to non-numeric index");
    }


    public function providerDataWithNonNumericIndexLocator()
    {
        return [
            'rootEmptyArray' => [
                '/a',
                [],
                1,
                ['a' => 1],
            ],
            'rootArrayExistingIndex' => [
                '/a',
                [1, 'a' => 2, 3],
                4,
                [1, 'a' => 4, 3],
            ],
            'rootArrayNewIndex' => [
                '/a',
                [1, 2, 3],
                4,
                [1, 2, 3, 'a' => 4],
            ],
            'nestedEmptyArray' => [
                '/1/a',
                [1, [], 2],
                3,
                [1, ['a' => 3], 2],
            ],
            'nestedArrayExistingIndex' => [
                '/1/a',
                [1, ['a' => 2], 3],
                4,
                [1, ['a' => 4], 3],
            ],
            'nestedArrayNewIndex' => [
                '/1/a',
                [1, [2, 3], 4],
                5,
                [1, [2, 3, 'a' => 5], 4],
            ],
        ];
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @dataProvider providerDataWithNumericIndexGapsLocator
     * @expectedException \Remorhaz\JSONPointer\Pointer\Evaluate\Exception
     */
    public function testWriteNumericIndexGapsThrowsException($text, $data, $value)
    {
        $locator = Parser::factory()
            ->setText($text)
            ->getLocator();
        LocatorWrite::factory()
            ->setData($data)
            ->setLocator($locator)
            ->setValue($value)
            ->perform();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @dataProvider providerDataWithNumericIndexGapsLocator
     * @expectedException \Remorhaz\JSONPointer\EvaluateException
     */
    public function testWriteNumericIndexGapsThrowsEvaluateException($text, $data, $value)
    {
        $locator = Parser::factory()
            ->setText($text)
            ->getLocator();
        LocatorWrite::factory()
            ->setData($data)
            ->setLocator($locator)
            ->setValue($value)
            ->perform();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @dataProvider providerDataWithNumericIndexGapsLocator
     * @expectedException \RuntimeException
     */
    public function testWriteNumericIndexGapsThrowsSplException($text, $data, $value)
    {
        $locator = Parser::factory()
            ->setText($text)
            ->getLocator();
        LocatorWrite::factory()
            ->setData($data)
            ->setLocator($locator)
            ->setValue($value)
            ->perform();
    }


    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $value
     * @param mixed $expectedData
     * @dataProvider providerDataWithNumericIndexGapsLocator
     */
    public function testWriteAllowedNumericIndexGaps($text, $data, $value, $expectedData)
    {
        $locator = Parser::factory()
            ->setText($text)
            ->getLocator();
        LocatorWrite::factory()
            ->setData($data)
            ->setLocator($locator)
            ->setValue($value)
            ->allowNumericIndexGaps()
            ->perform();
        $this->assertEquals($expectedData, $data, "Incorrect data after writing to index with gap");
    }


    public function providerDataWithNumericIndexGapsLocator()
    {
        return [
            'rootEmptyArray' => [
                '/1',
                [],
                1,
                [1 => 1],
            ],
            'rootArray' => [
                '/2',
                [1],
                2,
                [1, 2 => 2],
            ],
            'nestedEmptyArray' => [
                '/1/1',
                [1, []],
                2,
                [1, [1 => 2]],
            ],
            'nestedArray' => [
                '/1/2',
                [1, [2]],
                3,
                [1, [2, 2 => 3]],
            ],
        ];
    }
}
