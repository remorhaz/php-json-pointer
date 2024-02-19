<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Parser;

use Remorhaz\JSON\Pointer\Locator\LocatorBuilder;
use Remorhaz\JSON\Pointer\Locator\LocatorInterface;
use Remorhaz\JSON\Pointer\Locator\ReferenceFactory;
use Remorhaz\JSON\Pointer\Locator\ReferenceFactoryInterface;
use Remorhaz\UniLex\Exception as UniLexException;

final class Parser implements ParserInterface
{
    public static function create(): ParserInterface
    {
        return new self(
            new Ll1ParserFactory(),
            new ReferenceFactory(),
        );
    }

    public function __construct(
        private readonly Ll1ParserFactoryInterface $ll1ParserFactory,
        private readonly ReferenceFactoryInterface $referenceFactory,
    ) {
    }

    /**
     * @throws UniLexException
     */
    public function buildLocator(string $pointer): LocatorInterface
    {
        $locatorBuilder = new LocatorBuilder($this->referenceFactory);
        $this
            ->ll1ParserFactory
            ->createParser($pointer, $locatorBuilder)
            ->run();

        return $locatorBuilder->getLocator();
    }
}
