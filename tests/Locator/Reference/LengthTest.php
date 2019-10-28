<?php

namespace Remorhaz\JSON\Pointer\Test\Locator\Reference;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\DomainException as LocatorDomainException;
use Remorhaz\JSON\Pointer\Locator\LogicException as LocatorLogicException;
use Remorhaz\JSON\Pointer\Locator\Reference;

class LengthTest extends TestCase
{

    public function testAccessingUninitializedLengthThrowsException()
    {
        $factory = Reference::factory();
        $this->expectException(LocatorLogicException::class);
        $factory->getLength();
    }

    /**
     * @param int $length
     * @dataProvider providerValidLength
     */
    public function testGotLengthSameAsSet(int $length)
    {
        $reference = Reference::factory()->setLength($length);
        $this->assertEquals($length, $reference->getLength(), "Got length differs from set one");
    }

    public function providerValidLength(): array
    {
        return [
            'zeroLength' => [0],
            'oneLength' => [1],
            'maxLength' => [PHP_INT_MAX],
        ];
    }

    public function testSetNegativeLengthThrowsException()
    {
        $factory = Reference::factory();
        $this->expectException(LocatorDomainException::class);
        $factory->setLength(-1);
    }
}
