<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Locator;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\ListedReferenceInterface;
use Remorhaz\JSON\Pointer\Locator\Locator;
use Remorhaz\JSON\Pointer\Locator\ReferenceInterface;

use function array_fill;
use function array_map;

/**
 * @covers \Remorhaz\JSON\Pointer\Locator\Locator
 */
class LocatorTest extends TestCase
{
    public function testReferences_ConstructedWithoutReferences_ReturnsEmptyList(): void
    {
        $locator = new Locator();
        self::assertSame([], $locator->references());
    }

    public function testReferences_ConstructedWithSingleReference_ResultListsSameReferenceInstance(): void
    {
        $reference = $this->createMock(ReferenceInterface::class);
        $locator = new Locator($reference);
        $references = array_map(
            function (ListedReferenceInterface $listedReference): ReferenceInterface {
                return $listedReference->getReference();
            },
            $locator->references()
        );
        self::assertSame([$reference], $references);
    }

    /**
     * @param int   $referenceCount
     * @param array $expectedValue
     * @dataProvider providerIsLast
     */
    public function testReferences_Constructed_ResultListsMatchingIsLastState(
        int $referenceCount,
        array $expectedValue
    ): void {
        $references = array_fill(
            0,
            $referenceCount,
            $this->createMock(ReferenceInterface::class)
        );
        $locator = new Locator(...$references);
        $isLastStates = array_map(
            function (ListedReferenceInterface $listedReference): bool {
                return $listedReference->isLast();
            },
            $locator->references()
        );
        self::assertSame($expectedValue, $isLastStates);
    }

    public function providerIsLast(): array
    {
        return [
            'No references' => [0, []],
            'Single reference' => [1, [true]],
            'Two references' => [2, [false, true]],
        ];
    }
}
