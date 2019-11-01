<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Query;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\ValueDecoder;
use Remorhaz\JSON\Data\Export\ValueEncoder;
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Pointer\Locator\Locator;
use Remorhaz\JSON\Pointer\Locator\Reference;
use Remorhaz\JSON\Pointer\Query\Query;

/**
 * @covers \Remorhaz\JSON\Pointer\Query\Query
 */
class QueryTest extends TestCase
{

    public function testGetSource_ConstructedWithSource_ReturnsSameValue(): void
    {
        $query = new Query(
            'a',
            $this->createMock(Locator::class),
        );
        self::assertSame('a', $query->getSource());
    }

    public function testInvoke_ValidPointerDataExists_ReturnsMatchingValue(): void
    {
        $reference = (new Reference)
            ->setType(Reference::TYPE_PROPERTY)
            ->setKey('a');
        $locator = (new Locator)->addReference($reference);
        $query = new Query('', $locator);
        $nodeValue = NodeValueFactory::create()->createValue('{"a":"b"}');
        $resultNode = $query($nodeValue)->getSelection();

        $exporter = new ValueEncoder(new ValueDecoder);
        self::assertSame('"b"', $exporter->exportValue($resultNode));
    }
}
