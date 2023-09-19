<?php

namespace Remorhaz\JSON\Pointer\Test\Pointer;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Pointer\Processor\Processor;
use Remorhaz\JSON\Pointer\Query\QueryFactory;

/**
 * @coversNothing
 */
class AcceptanceTest extends TestCase
{
    /**
     * @dataProvider providerSelectExistingData
     */
    public function testSelect_ExistingData_SelectsMatchingResult(string $text, string $data, string $result)
    {
        $query = QueryFactory::create()->createQuery($text);
        $document = NodeValueFactory::create()->createValue($data);
        $readData = Processor::create()
            ->select($query, $document)
            ->encode();

        $this->assertSame($result, $readData);
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function providerSelectExistingData(): iterable
    {
        return [
            'rootProperty' => ['/a', '{"a":1,"b":2}', '1'],
            'rootNumericProperty' => ['/1', '{"1":2,"b":3}', '2'],
            'rootNegativeNumericProperty' => ['/-1', '{"-1":2,"b":3}', '2'],
            'nestedProperty' => ['/a/b', '{"a":{"b":1},"c":2}', '1'],
            'rootIndex' => ['/1', '[1,2]', '2'],
            'nestedIndex' => ['/0/1', '[[1,2],3]', '2'],
            'rootScalar' => ['', '"abc"', '"abc"'],
            'rootNull' => ['', 'null', 'null'],
            'nestedNull' => ['/a', '{"a":null}', 'null'],
        ];
    }

    /**
     * @dataProvider providerSelectNonExistingData
     */
    public function testSelect_NonExistingData_ResultNotExists(string $text, string $data)
    {
        $query = QueryFactory::create()->createQuery($text);
        $document = NodeValueFactory::create()->createValue($data);
        $actualValue = Processor::create()
            ->select($query, $document)
            ->exists();

        self::assertFalse($actualValue);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function providerSelectNonExistingData(): iterable
    {
        return [
            'nonExistingRootProperty' => ['/a', '{"b":1}'],
            'nonExistingNestedProperty' => ['/a/b', '{"a":{"c":1}}'],
            'nonExistingRootNumericIndex' => ['/1', '[1]'],
            'nonExistingNestedNumericIndex' => ['/0/1', '[[1],2]'],
            'rootArrayProperty' => ['/a', '[]'],
            'nestedArrayProperty' => ['/a/b', '{"a":[]}'],
            'rootScalarKey' => ['/a', '1'],
            'nestedScalarKey' => ['/a/b', '{"a":1}'],
            'nonExistingRootNonNumericIndex' => ['/a', '[1]'],
            'nonExistingNestedNonNumericIndex' => ['/0/a', '[[1],2]'],
            'rootNextIndex' => ['/-', '[1,2]'],
            'nestedNextIndex' => ['/a/-', '{"a":[1,2]}'],
        ];
    }

    /**
     * @dataProvider providerAddableQuery
     */
    public function testAdd_AddableQuery_DataAdded(string $data, string $query, string $value, string $expectedData)
    {
        $query = QueryFactory::create()->createQuery($query);
        $nodeValueFactory = NodeValueFactory::create();
        $document = $nodeValueFactory->createValue($data);
        $replacement = $nodeValueFactory->createValue($value);
        $actualData = Processor::create()
            ->add($query, $document, $replacement)
            ->encode();

        $this->assertEquals($expectedData, $actualData);
    }

    /**
     * @return iterable<string, array{string, string, string, string}>
     */
    public static function providerAddableQuery(): iterable
    {
        return [
            'Property exists' => ['{"a":"b"}', '/a', '"c"', '{"a":"c"}'],
            'Property not exists' => ['{"a":"b"}', '/c', '"d"', '{"a":"b","c":"d"}'],
            'Element exists' => ['[1,3]', '/1', '2', '[1,2,3]'],
            'Next element not exists' => ['[1,2]', '/2', '3', '[1,2,3]'],
            'New element' => ['[1,2]', '/-', '3', '[1,2,3]'],

        ];
    }

    /**
     * @dataProvider providerAddNonExistingSelection
     */
    public function testAdd_NonAddableQuery_ResultNotExists(string $data, string $text, string $value)
    {
        $query = QueryFactory::create()->createQuery($text);
        $nodeValueFactory = NodeValueFactory::create();
        $document = $nodeValueFactory->createValue($data);
        $replacement = $nodeValueFactory->createValue($value);
        $result = Processor::create()->add($query, $document, $replacement);

        self::assertFalse($result->exists());
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function providerAddNonExistingSelection(): iterable
    {
        return [
            'Not next element not exists' => ['[1,2]', "/3", '4'],
            'Invalid element index' => ['[1,2]', "/a", '3'],
            'Parent index not exists' => ['[1,2]', "/2/0", '4'],
            'Parent new index' => ['[1,2]', "/-/0", '4'],
            'Parent property not exists' => ['{"a":"b"}', "/c/d", '"e"'],
            'Scalar parent' => ['"a"', "/a", '"b"'],
        ];
    }

    /**
     * @dataProvider providerRemoveExistingData
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
     * @return iterable<string, array{string, string, string}>
     * @todo Shorten dataset list.
     */
    public static function providerRemoveExistingData(): iterable
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
     * @dataProvider providerRemoveNonExistingData
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

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function providerRemoveNonExistingData(): iterable
    {
        return [
            'LocatorPointsToWholeDocument' => ['', '{"a":"b"}'],
            'NonExistingElement' => ['/0/2', '[[1,[2,[3]]]]'],
            'LocatorContainsScalar' => ['/a/b', '{"a":"b"}'],
            'LocatorContainsNewIndex' => ['/0/1/-', '[[1,[2,[3]]]]'],
        ];
    }

    /**
     * @dataProvider providerReplaceValueExists
     */
    public function testReplace_ValueExists_DataReplaced(
        string $data,
        string $text,
        string $value,
        string $expectedData,
    ) {
        $query = QueryFactory::create()->createQuery($text);
        $nodeValueFactory = NodeValueFactory::create();
        $document = $nodeValueFactory->createValue($data);
        $replacement = $nodeValueFactory->createValue($value);
        $actualData = Processor::create()
            ->replace($query, $document, $replacement)
            ->encode();

        self::assertSame($expectedData, $actualData);
    }

    /**
     * @return iterable<string, array{string, string, string, string}>
     */
    public static function providerReplaceValueExists(): iterable
    {
        return [
            'element' => ['[1]', "/0", '2', '[2]'],
            'property' => ['{"a":"b"}', "/a", '"c"', '{"a":"c"}'],
            'root' => ['"a"', "", '"b"', '"b"'],
        ];
    }

    /**
     * @dataProvider providerReplaceValueNotExists
     */
    public function testReplace_ValueNotExists_ResultNotExists(
        string $data,
        string $text,
        string $value,
    ) {
        $query = QueryFactory::create()->createQuery($text);
        $nodeValueFactory = NodeValueFactory::create();
        $document = $nodeValueFactory->createValue($data);
        $replacement = $nodeValueFactory->createValue($value);
        $actualData = Processor::create()
            ->replace($query, $document, $replacement)
            ->exists();

        self::assertFalse($actualData);
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function providerReplaceValueNotExists(): iterable
    {
        return [
            'ElementNotExists' => ['[1]', '/1', '2'],
            'PropertyNotExists' => ['{"a":"b"}', '/c', '"d"'],
            'ScalarSelection' => ['"a"', '/a', '"b"'],
            'propertyReference' => ['[1]', "/a", '2'],
            'newIndexReference' => ['[1]', "/-", '2'],
        ];
    }
}
