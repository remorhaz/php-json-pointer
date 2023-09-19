<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Parser\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Parser\Exception\LL1ParserNotCreatedException;

/**
 * @covers \Remorhaz\JSON\Pointer\Parser\Exception\LL1ParserNotCreatedException
 */
class LL1ParserNotCreatedExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new LL1ParserNotCreatedException();
        self::assertSame('Failed to create LL(1) parser', $exception->getMessage());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new LL1ParserNotCreatedException();
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new LL1ParserNotCreatedException();
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new LL1ParserNotCreatedException($previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
