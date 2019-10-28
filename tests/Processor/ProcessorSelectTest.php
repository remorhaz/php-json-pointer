<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Pointer;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Pointer\Processor\Exception\QueryNotSelectableException;
use Remorhaz\JSON\Pointer\Processor\Processor;
use Remorhaz\JSON\Pointer\Query\QueryFactory;

/**
 * @covers \Remorhaz\JSON\Pointer\Processor\Processor
 */
class ProcessorSelectTest extends TestCase
{

    /**
     * @param string $text
     * @param mixed $data
     * @param mixed $result
     * @dataProvider providerExistingData
     */
    public function testReadExistingData(string $text, $data, $result)
    {
        $query = QueryFactory::create()->createQuery($text);
        $document = NodeValueFactory::create()->createValue($data);
        $readData = Processor::create()
            ->select($query, $document)
            ->encode();

        $this->assertSame($result, $readData);
    }

    public function providerExistingData(): array
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
     * @param string $text
     * @param mixed $data
     * @dataProvider providerNonExistingData
     */
    public function testReadNonExistingData_ResultNotExists(string $text, $data)
    {
        $query = QueryFactory::create()->createQuery($text);
        $document = NodeValueFactory::create()->createValue($data);
        $actualValue = Processor::create()
            ->select($query, $document)
            ->exists();

        self::assertFalse($actualValue);
    }

    public function providerNonExistingData(): array
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
        ];
    }

    /**
     * @param string $text
     * @param mixed $data
     * @dataProvider providerPointsNewElement
     */
    public function testPointsNewElement_ResultNotExists(string $text, $data)
    {
        $query = QueryFactory::create()->createQuery($text);
        $document = NodeValueFactory::create()->createValue($data);
        $processor = Processor::create();

        $this->expectException(QueryNotSelectableException::class);
        $processor->select($query, $document);
    }

    public function providerPointsNewElement(): array
    {
        return [
            'rootNextIndex' => ['/-', '[1,2]'],
            'nestedNextIndex' => ['/a/-', '{"a":[1,2]}'],
        ];
    }
}
