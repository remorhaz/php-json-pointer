<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Query\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Query\Exception\LastReferenceNotFoundException;

/**
 * @covers \Remorhaz\JSON\Pointer\Query\Exception\LastReferenceNotFoundException
 */
class LastReferenceNotFoundExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new LastReferenceNotFoundException('a');
        self::assertSame('Query \'a\' selected no last reference', $exception->getMessage());
    }

    public function testGetSource_ConstructedWithSource_ReturnsSameValue(): void
    {
        $exception = new LastReferenceNotFoundException('a');
        self::assertSame('a', $exception->getSource());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new LastReferenceNotFoundException('a');
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new LastReferenceNotFoundException('a');
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new LastReferenceNotFoundException('a', $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
