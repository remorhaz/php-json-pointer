<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Locator;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\ListedReference;
use Remorhaz\JSON\Pointer\Locator\ReferenceInterface;

#[CoversClass(ListedReference::class)]
class ListedReferenceTest extends TestCase
{
    public function testGetReference_ConstructedWithReference_ReturnsSameInstance(): void
    {
        $reference = self::createStub(ReferenceInterface::class);
        $listedReference = new ListedReference($reference, false);
        self::assertSame($reference, $listedReference->getReference());
    }

    #[DataProvider('providerIsLast')]
    public function testIsLast_ConstructedWithValue_ReturnsSameValue(bool $isValue, bool $expectedValue): void
    {
        $listedReference = new ListedReference(
            self::createStub(ReferenceInterface::class),
            $isValue
        );
        self::assertSame($expectedValue, $listedReference->isLast());
    }

    /**
     * @return iterable<string, array{bool, bool}>
     */
    public static function providerIsLast(): iterable
    {
        return [
            'TRUE' => [true, true],
            'FALSE' => [false, false],
        ];
    }
}
