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
}
