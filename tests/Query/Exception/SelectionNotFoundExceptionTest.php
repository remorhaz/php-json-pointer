<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Query\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Query\Exception\SelectionNotFoundException;

/**
 * @covers \Remorhaz\JSON\Pointer\Query\Exception\SelectionNotFoundException
 */
class SelectionNotFoundExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new SelectionNotFoundException('a');
        self::assertSame('Query \'a\' produced no selection', $exception->getMessage());
    }

    public function testGetSource_ConstructedWithSource_ReturnsSameValue(): void
    {
        $exception = new SelectionNotFoundException('a');
        self::assertSame('a', $exception->getSource());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new SelectionNotFoundException('a');
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new SelectionNotFoundException('a');
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new SelectionNotFoundException('a', $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
