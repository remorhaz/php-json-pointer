<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Locator;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\ListedReference;
use Remorhaz\JSON\Pointer\Locator\ReferenceInterface;

/**
 * @covers \Remorhaz\JSON\Pointer\Locator\ListedReference
 */
class ListedReferenceTest extends TestCase
{

    public function testGetReference_ConstructedWithReference_ReturnsSameInstance(): void
    {
        $reference = $this->createMock(ReferenceInterface::class);
        $listedReference = new ListedReference($reference, false);
        self::assertSame($reference, $listedReference->getReference());
    }

    /**
     * @param bool $isValue
     * @param bool $expectedValue
     * @dataProvider providerIsLast
     */
    public function testIsLast_ConstructedWithValue_ReturnsSameValue(bool $isValue, bool $expectedValue): void
    {
        $listedReference = new ListedReference(
            $this->createMock(ReferenceInterface::class),
            $isValue
        );
        self::assertSame($expectedValue, $listedReference->isLast());
    }

    public function providerIsLast(): array
    {
        return [
            'TRUE' => [true, true],
            'FALSE' => [false, false],
        ];
    }
}
