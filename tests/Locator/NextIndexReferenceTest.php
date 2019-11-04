<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Locator;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\NextIndexReference;

/**
 * @covers \Remorhaz\JSON\Pointer\Locator\NextIndexReference
 */
class NextIndexReferenceTest extends TestCase
{

    public function testGetPropertyName_Always_ReturnsHyphen(): void
    {
        $reference = new NextIndexReference;
        self::assertSame('-', $reference->getPropertyName());
    }
}
