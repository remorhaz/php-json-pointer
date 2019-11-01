<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Locator;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\DomainException as LocatorDomainException;
use Remorhaz\JSON\Pointer\Locator\LogicException as LocatorLogicException;
use Remorhaz\JSON\Pointer\Locator\Reference;

/**
 * @covers \Remorhaz\JSON\Pointer\Locator\Reference
 */
class ReferenceTest extends TestCase
{

    public function testIsLast_IsLastNotSet_ThrowsException()
    {
        $reference = new Reference;
        $this->expectException(LocatorLogicException::class);
        $reference->isLast();
    }

    public function testGetLength_LengthNotSet_ThrowsException()
    {
        $reference = new Reference;
        $this->expectException(LocatorLogicException::class);
        $reference->getLength();
    }

    /**
     * @param int $length
     * @dataProvider providerValidLength
     */
    public function testGetLength_GivenLengthSet_RweturnsSameValue(int $length)
    {
        $reference = (new Reference)->setLength($length);
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

    public function testSetLength_NegativeLength_ThrowsException()
    {
        $reference = new Reference;
        $this->expectException(LocatorDomainException::class);
        $reference->setLength(-1);
    }

    public function testGetType_TypeNotSet_ThrowsException()
    {
        $reference = new Reference;
        $this->expectException(LocatorLogicException::class);
        $reference->getType();
    }

    public function testGetType_GivenTypeSet_ReturnsSameValue()
    {
        $type = Reference::TYPE_INDEX;
        $reference = (new Reference)->setType($type);
        $this->assertEquals($type, $reference->getType(), "Got type differs from the one that was set");
    }

    public function testSetType_InvalidType_ThrowsException()
    {
        $reference = new Reference;
        $this->expectException(LocatorDomainException::class);
        $reference->setType(0xFF);
    }

    public function testGetKey_KeyNotSet_ThrowsException()
    {
        $reference = new Reference;
        $this->expectException(LocatorLogicException::class);
        $reference->getKey();
    }

    public function testGetKey_GivenKeySet_ReturnsSameValue()
    {
        $value = 'abc';
        $reference = (new Reference)->setKey($value);
        $this->assertEquals($value, $reference->getKey());
    }
}
