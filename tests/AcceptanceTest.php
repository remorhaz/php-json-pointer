<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Pointer\Processor\Processor;
use Remorhaz\JSON\Pointer\Query\QueryFactory;

/**
 * Examples from RFC-6901
 *
 * @see https://tools.ietf.org/html/rfc6901
 * @coversNothing
 */
class AcceptanceTest extends TestCase
{

    /**
     * @param string $document
     * @param string $pointer
     * @param string $expectedValue
     * @dataProvider providerSelect
     */
    public function testSelect(string $document, string $pointer, string $expectedValue): void
    {
        $rootNode = NodeValueFactory::create()->createValue($document);
        $query = QueryFactory::create()->createQuery($pointer);
        $selection = Processor::create()
            ->select($query, $rootNode)
            ->encode();
        self::assertSame($expectedValue, $selection);
    }

    public function providerSelect(): array
    {
        $document = '{"foo":["bar","baz"],"":0,"a/b":1,"c%d":2,"e^f":3,"g|h":4,"i\\\\j":5,"k\\"l":6," ":7,"m~n":8}';

        return [
            [$document, "", $document],
            [$document, "/foo", '["bar","baz"]'],
            [$document, "/foo/0", '"bar"'],
            [$document, "/", '0'],
            [$document, "/a~1b", '1'],
            [$document, "/c%d", '2'],
            [$document, "/e^f", '3'],
            [$document, "/g|h", '4'],
            [$document, "/i\\j", '5'],
            [$document, "/k\"l", '6'],
            [$document, "/ ", '7'],
            [$document, "/m~0n", '8'],
        ];
    }

    /**
     * @param string $document
     * @param string $pointer
     * @param string $expectedValue
     * @dataProvider providerDelete
     */
    public function testDelete(string $document, string $pointer, string $expectedValue): void
    {
        $rootNode = NodeValueFactory::create()->createValue($document);
        $query = QueryFactory::create()->createQuery($pointer);
        $selection = Processor::create()
            ->delete($query, $rootNode)
            ->encode();
        self::assertSame($expectedValue, $selection);
    }

    public function providerDelete(): array
    {
        return [
            'First property' => ['{"a":"b","c":"d"}', '/a', '{"c":"d"}'],
            'Last property' => ['{"a":"b","c":"d"}', '/c', '{"a":"b"}'],
            'Nested inner property' => [
                '{"a":{"b":"c","d":"e","f":"g"},"h":"i"}',
                '/a/d',
                '{"a":{"b":"c","f":"g"},"h":"i"}',
            ],
            'First element' => ['[1,2]', '/0', '[2]'],
            'Last element' => ['[1,2]', '/1', '[1]'],
            'Nested inner element' => ['[1,[2,3,4],5]', '/1/1', '[1,[2,4],5]'],
        ];
    }

    /**
     * @param string $document
     * @param string $pointer
     * @param string $value
     * @param string $expectedValue
     * @dataProvider providerAdd
     */
    public function testAdd(string $document, string $pointer, string $value, string $expectedValue): void
    {
        $rootNode = NodeValueFactory::create()->createValue($document);
        $valueNode = NodeValueFactory::create()->createValue($value);
        $query = QueryFactory::create()->createQuery($pointer);
        $selection = Processor::create()
            ->add($query, $rootNode, $valueNode)
            ->encode();
        self::assertSame($expectedValue, $selection);
    }

    public function providerAdd(): array
    {
        return [
            'Add element to empty array' => ['[]', '/0', '1', '[1]'],
            'Add property to empty object' => ['{}', '/a', '"b"', '{"a":"b"}'],
            'Replace root array with object' => ['[1]', '', '{"a":"b"}', '{"a":"b"}'],
            'Replace root scalar' => ['1', '', '"a"', '"a"'],
            'Insert array element' => ['[1,2]', '/1', '3', '[1,3,2]'],
            'Append array element by index' => ['[1,2]', '/2', '3', '[1,2,3]'],
            'Auto-append array element' => ['[1,2]', '/-', '3', '[1,2,3]'],
        ];
    }
}
