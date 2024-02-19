<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Locator;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\NextIndexReference;

#[CoversClass(NextIndexReference::class)]
class NextIndexReferenceTest extends TestCase
{
    public function testGetPropertyName_Always_ReturnsHyphen(): void
    {
        $reference = new NextIndexReference();
        self::assertSame('-', $reference->getPropertyName());
    }
}
