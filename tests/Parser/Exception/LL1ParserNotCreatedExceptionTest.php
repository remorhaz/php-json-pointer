<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Parser\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Parser\Exception\LL1ParserNotCreatedException;

#[CoversClass(LL1ParserNotCreatedException::class)]
class LL1ParserNotCreatedExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new LL1ParserNotCreatedException();
        self::assertSame('Failed to create LL(1) parser', $exception->getMessage());
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
