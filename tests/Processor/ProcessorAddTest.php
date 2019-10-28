<?php

namespace Remorhaz\JSON\Pointer\Test\Pointer;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Pointer\Processor\Processor;
use Remorhaz\JSON\Pointer\Query\QueryFactory;

class ProcessorAddTest extends TestCase
{

    /**
     * @param string $data
     * @param string $query
     * @param string $value
     * @param string $expectedData
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

    public function providerAddableQuery(): array
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
     * @param string $data
     * @param string $text
     * @param string $value
     * @dataProvider providerNonExistingSelection
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

    public function providerNonExistingSelection(): array
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
}
