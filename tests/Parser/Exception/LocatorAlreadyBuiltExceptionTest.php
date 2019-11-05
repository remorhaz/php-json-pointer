<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Parser\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Parser\Exception\LocatorAlreadyBuiltException;

/**
 * @covers \Remorhaz\JSON\Pointer\Parser\Exception\LocatorAlreadyBuiltException
 */
class LocatorAlreadyBuiltExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new LocatorAlreadyBuiltException;
        self::assertSame('Locator is already built', $exception->getMessage());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new LocatorAlreadyBuiltException;
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new LocatorAlreadyBuiltException;
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new LocatorAlreadyBuiltException($previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
