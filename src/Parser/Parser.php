<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Parser;

use Remorhaz\JSON\Pointer\Locator\Locator;
use Remorhaz\JSON\Pointer\Locator\LocatorInterface;
use Remorhaz\UniLex\Exception as UniLexException;

final class Parser implements ParserInterface
{

    private $ll1ParserFactory;

    public static function create(): ParserInterface
    {
        $ll1ParserFactory = new Ll1ParserFactory;

        return new self($ll1ParserFactory);
    }

    public function __construct(Ll1ParserFactoryInterface $ll1ParserFactory)
    {
        $this->ll1ParserFactory = $ll1ParserFactory;
    }

    /**
     * @param string $pointer
     * @return LocatorInterface
     * @throws UniLexException
     */
    public function buildLocator(string $pointer): LocatorInterface
    {
        $locator = Locator::factory();
        $this
            ->ll1ParserFactory
            ->createParser($pointer, $locator)
            ->run();

        return $locator;
    }
}
