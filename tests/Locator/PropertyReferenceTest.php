<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Locator;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\PropertyReference;

/**
 * @covers \Remorhaz\JSON\Pointer\Locator\PropertyReference
 */
class PropertyReferenceTest extends TestCase
{
    public function testGetPropertyName_ConstructedWithPropertyName_ReturnsSameValue(): void
    {
        $reference = new PropertyReference('a');
        self::assertSame('a', $reference->getPropertyName());
    }
}
