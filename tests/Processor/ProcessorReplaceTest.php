<?php

namespace Remorhaz\JSON\Pointer\Test\Processor;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Pointer\Processor\Processor;
use Remorhaz\JSON\Pointer\Query\QueryFactory;

class ProcessorReplaceTest extends TestCase
{

    /**
     * @param mixed  $data
     * @param string $text
     * @param string $value
     * @param string $expectedData
     * @dataProvider providerValueExists
     */
    public function testReplace_ValueExists_DataReplaced(
        string $data,
        string $text,
        string $value,
        string $expectedData
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

    public function providerValueExists(): array
    {
        return [
            'element' => ['[1]', "/0", '2', '[2]'],
            'property' => ['{"a":"b"}', "/a", '"c"', '{"a":"c"}'],
            'root' => ['"a"', "", '"b"', '"b"'],
        ];
    }

    /**
     * @param string $data
     * @param string $text
     * @param string $value
     * @dataProvider providerValueNotExists
     */
    public function testReplace_ValueNotExists_ResultNotExists(
        string $data,
        string $text,
        string $value
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

    public function providerValueNotExists(): array
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
