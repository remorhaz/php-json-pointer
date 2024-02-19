<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Processor\Result;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Pointer\Processor\Result\ExistingResult;

#[CoversClass(ExistingResult::class)]
class ExistingResultTest extends TestCase
{
    public function testExists_Always_ReturnsTrue(): void
    {
        $result = new ExistingResult(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            self::createStub(NodeValueInterface::class),
        );
        self::assertTrue($result->exists());
    }

    public function testEncode_ConstructedWithValue_PassesSameInstanceToEncoder(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $value = self::createStub(NodeValueInterface::class);
        $result = new ExistingResult(
            $encoder,
            self::createStub(ValueDecoderInterface::class),
            $value,
        );

        $encoder
            ->expects(self::once())
            ->method('exportValue')
            ->with($value);
        $result->encode();
    }

    public function testEncode_EncoderExportsValue_ReturnsSameValue(): void
    {
        $encoder = self::createStub(ValueEncoderInterface::class);
        $value = self::createStub(NodeValueInterface::class);
        $result = new ExistingResult(
            $encoder,
            self::createStub(ValueDecoderInterface::class),
            $value,
        );

        $encoder
            ->method('exportValue')
            ->willReturn('a');
        self::assertSame('a', $result->encode());
    }

    public function testDecode_ConstructedWithValue_PassesSameInstanceToDecoder(): void
    {
        $decoder = $this->createMock(ValueDecoderInterface::class);
        $value = self::createStub(NodeValueInterface::class);
        $result = new ExistingResult(
            self::createStub(ValueEncoderInterface::class),
            $decoder,
            $value,
        );

        $decoder
            ->expects(self::once())
            ->method('exportValue')
            ->with($value);
        $result->decode();
    }

    public function testDecode_DecoderExportsValue_ReturnsSameValue(): void
    {
        $decoder = self::createStub(ValueDecoderInterface::class);
        $value = self::createStub(NodeValueInterface::class);
        $result = new ExistingResult(
            self::createStub(ValueEncoderInterface::class),
            $decoder,
            $value,
        );

        $decoder
            ->method('exportValue')
            ->willReturn('a');
        self::assertSame('a', $result->decode());
    }

    public function testGet_ConstructedWithValue_ReturnsSameInstance(): void
    {
        $value = self::createStub(NodeValueInterface::class);
        $result = new ExistingResult(
            self::createStub(ValueEncoderInterface::class),
            self::createStub(ValueDecoderInterface::class),
            $value,
        );
        self::assertSame($value, $result->get());
    }
}
