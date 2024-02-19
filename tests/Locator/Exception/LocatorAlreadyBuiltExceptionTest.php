<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Locator\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\Exception\LocatorAlreadyBuiltException;

#[CoversClass(LocatorAlreadyBuiltException::class)]
class LocatorAlreadyBuiltExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new LocatorAlreadyBuiltException();
        self::assertSame('Locator is already built', $exception->getMessage());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new LocatorAlreadyBuiltException();
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new LocatorAlreadyBuiltException($previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
