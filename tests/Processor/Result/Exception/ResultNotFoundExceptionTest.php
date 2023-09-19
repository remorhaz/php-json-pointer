<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Processor\Result\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Processor\Result\Exception\ResultNotFoundException;

/**
 * @covers \Remorhaz\JSON\Pointer\Processor\Result\Exception\ResultNotFoundException
 */
class ResultNotFoundExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new ResultNotFoundException('a');
        self::assertSame('Result not found for query \'a\'', $exception->getMessage());
    }

    public function testGetSource_ConstructedWithSource_ReturnsSameValue(): void
    {
        $exception = new ResultNotFoundException('a');
        self::assertSame('a', $exception->getSource());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new ResultNotFoundException('a');
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_PreviousNotSet_ReturnsNull(): void
    {
        $exception = new ResultNotFoundException('a');
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_GivenPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new ResultNotFoundException('a', $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
