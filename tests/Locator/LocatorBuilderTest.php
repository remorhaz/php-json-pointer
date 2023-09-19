<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Locator;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\ListedReferenceInterface;
use Remorhaz\JSON\Pointer\Locator\ReferenceFactory;
use Remorhaz\JSON\Pointer\Locator\ReferenceFactoryInterface;
use Remorhaz\JSON\Pointer\Locator\ReferenceInterface;
use Remorhaz\JSON\Pointer\Locator\Exception\LocatorAlreadyBuiltException;
use Remorhaz\JSON\Pointer\Locator\LocatorBuilder;

use function array_map;

/**
 * @covers \Remorhaz\JSON\Pointer\Locator\LocatorBuilder
 */
class LocatorBuilderTest extends TestCase
{
    public function testCreate_Always_ReturnsLocatorBuilderInstance(): void
    {
        self::assertInstanceOf(LocatorBuilder::class, LocatorBuilder::create());
    }

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

    public function testAddReference_ExportCalled_ThrowsException(): void
    {
        $builder = new LocatorBuilder($this->createMock(ReferenceFactoryInterface::class));
        $builder->export();
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

    public function testExport_NoReferencesAdded_ReturnsEmptyPointer(): void
    {
        $referenceFactory = $this->createMock(ReferenceFactoryInterface::class);
        $builder = new LocatorBuilder($referenceFactory);
        self::assertSame('', $builder->export());
    }

    /**
     * @dataProvider providerExportSingleReference
     */
    public function testExport_SingleReferenceAdded_ReturnsMatchingPointer(string $text, string $expectedValue): void
    {
        $builder = new LocatorBuilder(new ReferenceFactory());
        $builder->addReference($text);
        self::assertSame($expectedValue, $builder->export());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function providerExportSingleReference(): iterable
    {
        return [
            'ASCII string' => ['a', '/a'],
            'Tilde' => ['~', '/~0'],
            'Slash' => ['/', '/~1'],
            'Tilde after slash' => ['/~', '/~1~0'],
            'Slash after tilde' => ['~/', '/~0~1'],
        ];
    }

    public function testExport_TwoReferencesAdded_ReturnsMatchingPointer(): void
    {
        $builder = new LocatorBuilder(new ReferenceFactory());
        $builder->addReference('a');
        $builder->addReference('b');
        self::assertSame('/a/b', $builder->export());
    }
}
