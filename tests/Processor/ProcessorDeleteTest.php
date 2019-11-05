<?php

namespace Remorhaz\JSON\Pointer\Test\Pointer;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Pointer\Processor\Processor;
use Remorhaz\JSON\Pointer\Query\QueryFactory;

/**
 * @coversNothing
 */
class ProcessorDeleteTest extends TestCase
{

    /**
     * @param string $text
     * @param        $data
     * @param        $expectedData
     * @dataProvider providerExistingData
     */
    public function testRemove_ExistingData_Removed(string $text, string $data, string $expectedData)
    {
        $query = QueryFactory::create()->createQuery($text);
        $document = NodeValueFactory::create()->createValue($data);
        $actualValue = Processor::create()
            ->delete($query, $document)
            ->encode();

        $this->assertSame($expectedData, $actualValue);
    }

    /**
     * @return array
     * @todo Shorten dataset list.
     */
    public function providerExistingData(): array
    {
        return [
            'rootProperty' => [
                '/a',
                '{"a":1,"b":2}',
                '{"b":2}',
            ],
            'rootNumericProperty' => [
                '/1',
                '{"1":2,"b":3}',
                '{"b":3}',
            ],
            'rootNegativeNumericProperty' => [
                '/-1',
                '{"-1":2,"b":3}',
                '{"b":3}',
            ],
            'nestedProperty' => [
                '/a/b',
                '{"a":{"b":1},"c":2}',
                '{"a":{},"c":2}',
            ],
            'rootIndex' => ['/1', '[1,2]', '[1]'],
            'nestedIndex' => ['/0/1', '[[1,2],3]', '[[1],3]'],
            'nestedNull' => [
                '/a',
                '{"a":null}',
                '{}',
            ],
        ];
    }

    /**
     * @param string $text
     * @param string $data
     * @dataProvider providerNonExistingData
     */
    public function testRemove_NonExistingData_ResultNotExists(string $text, string $data)
    {
        $query = QueryFactory::create()->createQuery($text);
        $document = NodeValueFactory::create()->createValue($data);
        $actualValue = Processor::create()
            ->delete($query, $document)
            ->exists();

        self::assertFalse($actualValue);
    }

    public function providerNonExistingData(): array
    {
        return [
            'LocatorPointsToWholeDocument' => ['', '{"a":"b"}'],
            'NonExistingElement' => ['/0/2', '[[1,[2,[3]]]]'],
            'LocatorContainsScalar' => ['/a/b', '{"a":"b"}'],
            'LocatorContainsNewIndex' => ['/0/1/-', '[[1,[2,[3]]]]'],
        ];
    }
}
