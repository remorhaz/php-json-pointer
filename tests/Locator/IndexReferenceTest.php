<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Locator;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\IndexReference;

/**
 * @covers \Remorhaz\JSON\Pointer\Locator\IndexReference
 */
class IndexReferenceTest extends TestCase
{

    public function testGetElementIndex_ConstructedWithElementIndex_ReturnsSameValue(): void
    {
        $reference = new IndexReference(1);
        self::assertSame(1, $reference->getElementIndex());
    }

    public function testGetPropertyName_ConstructedWithElementIndex_ReturnsSameValueAsString(): void
    {
        $reference = new IndexReference(1);
        self::assertSame('1', $reference->getPropertyName());
    }
}
