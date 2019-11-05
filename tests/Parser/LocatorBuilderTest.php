<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\ListedReferenceInterface;
use Remorhaz\JSON\Pointer\Locator\ReferenceFactoryInterface;
use Remorhaz\JSON\Pointer\Locator\ReferenceInterface;
use Remorhaz\JSON\Pointer\Parser\Exception\LocatorAlreadyBuiltException;
use Remorhaz\JSON\Pointer\Parser\LocatorBuilder;
use function array_map;

/**
 * @covers \Remorhaz\JSON\Pointer\Parser\LocatorBuilder
 */
class LocatorBuilderTest extends TestCase
{

    public function testGetLocator_ReferencesNotAdded_ReturnsEmptyLocator(): void
    {
        $builder = new LocatorBuilder($this->createMock(ReferenceFactoryInterface::class));
        $locator = $builder->getLocator();
        self::assertCount(0, $locator->references());
    }

    public function testGetLocator_CalledTwice_ReturnsSameInstance(): void
    {
        $builder = new LocatorBuilder($this->createMock(ReferenceFactoryInterface::class));
        $locator = $builder->getLocator();
        self::assertSame($locator, $builder->getLocator());
    }

    public function testAddReference_GetLocatorCalled_ThrowsException(): void
    {
        $builder = new LocatorBuilder($this->createMock(ReferenceFactoryInterface::class));
        $builder->getLocator();
        $this->expectException(LocatorAlreadyBuiltException::class);
        $builder->addReference('a');
    }

    public function testAddReference_ConstructedWithReferenceFactory_PassesTextToSameInstance(): void
    {
        $referenceFactory = $this->createMock(ReferenceFactoryInterface::class);
        $builder = new LocatorBuilder($referenceFactory);

        $referenceFactory
            ->expects(self::once())
            ->method('createReference')
            ->with('a');
        $builder->addReference('a');
    }

    public function testAddReference_ConstructedWithReferenceFactory_ResultIsAddedToLocator(): void
    {
        $referenceFactory = $this->createMock(ReferenceFactoryInterface::class);
        $builder = new LocatorBuilder($referenceFactory);

        $firstReference = $this->createMock(ReferenceInterface::class);
        $secondReference = $this->createMock(ReferenceInterface::class);
        $referenceFactory
            ->method('createReference')
            ->willReturnOnConsecutiveCalls($firstReference, $secondReference);

        $builder->addReference('a');
        $builder->addReference('b');
        $listedReferences = $builder
            ->getLocator()
            ->references();
        $actualValue = array_map(
            function (ListedReferenceInterface $listedReference): ReferenceInterface {
                return $listedReference->getReference();
            },
            $listedReferences
        );
        self::assertSame([$firstReference, $secondReference], $actualValue);
    }
}
