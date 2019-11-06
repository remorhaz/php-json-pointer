<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Query\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Query\Exception\ParentNotFoundException;

/**
 * @covers \Remorhaz\JSON\Pointer\Query\Exception\ParentNotFoundException
 */
class ParentNotFoundExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new ParentNotFoundException('a');
        self::assertSame('Query \'a\' selected no parent node', $exception->getMessage());
    }

    public function testGetSource_ConstructedWithSource_ReturnsSameValue(): void
    {
        $exception = new ParentNotFoundException('a');
        self::assertSame('a', $exception->getSource());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new ParentNotFoundException('a');
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new ParentNotFoundException('a');
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new ParentNotFoundException('a', $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
