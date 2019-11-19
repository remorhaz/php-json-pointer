<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Processor\Result;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Processor\Result\Exception\ResultNotFoundException;
use Remorhaz\JSON\Pointer\Processor\Result\NonExistingResult;

/**
 * @covers \Remorhaz\JSON\Pointer\Processor\Result\NonExistingResult
 */
class NonExistingResultTest extends TestCase
{

    public function testExists_Always_ReturnsFalse(): void
    {
        $result = new NonExistingResult('a');
        self::assertFalse($result->exists());
    }

    public function testEncode_ConstructedWithSource_ThrowsMatchingException(): void
    {
        $result = new NonExistingResult('a');
        $this->expectException(ResultNotFoundException::class);
        $this->expectExceptionMessage('\'a\'');
        $result->encode();
    }

    public function testDecode_ConstructedWithSource_ThrowsMatchingException(): void
    {
        $result = new NonExistingResult('a');
        $this->expectException(ResultNotFoundException::class);
        $this->expectExceptionMessage('\'a\'');
        $result->decode();
    }

    public function testGet_ConstructedWithSource_ThrowsMatchingException(): void
    {
        $result = new NonExistingResult('a');
        $this->expectException(ResultNotFoundException::class);
        $this->expectExceptionMessage('\'a\'');
        $result->get();
    }
}
