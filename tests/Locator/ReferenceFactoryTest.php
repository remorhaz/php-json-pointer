<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Locator;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\IndexReference;
use Remorhaz\JSON\Pointer\Locator\IndexReferenceInterface;
use Remorhaz\JSON\Pointer\Locator\NextIndexReference;
use Remorhaz\JSON\Pointer\Locator\PropertyReference;
use Remorhaz\JSON\Pointer\Locator\ReferenceFactory;
use Remorhaz\JSON\Pointer\Locator\ReferenceInterface;
use function get_class;

/**
 * @covers \Remorhaz\JSON\Pointer\Locator\ReferenceFactory
 */
class ReferenceFactoryTest extends TestCase
{

    /**
     * @param string $text
     * @param array  $expectedValue
     * @dataProvider providerCreateReference
     */
    public function testCreateReference_GivenText_ReturnsMatchingReference(string $text, array $expectedValue): void
    {
        $factory = new ReferenceFactory;
        $reference = $factory->createReference($text);
        self::assertSame($expectedValue, $this->exportReference($reference));
    }

    public function providerCreateReference(): array
    {
        return [
            'Single hyphen' => [
                '-',
                [
                    'class' => NextIndexReference::class,
                    'propertyName' => '-',
                ],
            ],
            'Single zero' => [
                '0',
                [
                    'class' => IndexReference::class,
                    'propertyName' => '0',
                    'elementIndex' => 0,
                ],
            ],
            'Double zero' => [
                '00',
                [
                    'class' => PropertyReference::class,
                    'propertyName' => '00',
                ],
            ],
            'Single non-zero' => [
                '1',
                [
                    'class' => IndexReference::class,
                    'propertyName' => '1',
                    'elementIndex' => 1,
                ],
            ],
            'Digit sequence without zero prefix' => [
                '12',
                [
                    'class' => IndexReference::class,
                    'propertyName' => '12',
                    'elementIndex' => 12,
                ],
            ],
            'Digit sequence with zero prefix' => [
                '012',
                [
                    'class' => PropertyReference::class,
                    'propertyName' => '012',
                ],
            ],
            'Single non-digit character' => [
                'a',
                [
                    'class' => PropertyReference::class,
                    'propertyName' => 'a',
                ],
            ],
            'Non-digit character with zero prefix' => [
                '0a',
                [
                    'class' => PropertyReference::class,
                    'propertyName' => '0a',
                ],
            ],
        ];
    }

    private function exportReference(ReferenceInterface $reference): array
    {
        $data = [
            'class' => get_class($reference),
            'propertyName' => $reference->getPropertyName(),
        ];

        if ($reference instanceof IndexReferenceInterface) {
            $data += [
                'elementIndex' => $reference->getElementIndex(),
            ];
        }

        return $data;
    }
}
