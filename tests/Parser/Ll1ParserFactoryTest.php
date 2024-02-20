<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Test\Parser;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Pointer\Locator\LocatorBuilderInterface;
use Remorhaz\JSON\Pointer\Parser\Ll1ParserFactory;

#[CoversClass(Ll1ParserFactory::class)]
class Ll1ParserFactoryTest extends TestCase
{
    public function testCreateParser_GivenSourceAndLocatorBuilder_ResultAddsMatchingReferenceToSameBuilder(): void
    {
        $factory = new Ll1ParserFactory();
        $locatorBuilder = $this->createMock(LocatorBuilderInterface::class);
        $parser = $factory->createParser('/1', $locatorBuilder);
        $locatorBuilder
            ->expects(self::atLeastOnce())
            ->method('addReference')
            ->with(self::identicalTo('1'));
        $parser->run();
    }
}
