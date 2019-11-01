<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Parser;

use Remorhaz\JSON\Pointer\Locator\Locator;
use Remorhaz\JSON\Pointer\Locator\Reference;
use function intval;
use function preg_match;

final class LocatorBuilder implements LocatorBuilderInterface
{

    private $locator;

    public function __construct(Locator $locator)
    {
        $this->locator = $locator;
    }

    public function addReference(string $text): void
    {
        $reference = (new Reference)
            ->setKey($text)
            ->setText($text);

        $isIndex = 1 === preg_match('/^(?:0|[1-9][0-9]*)$/', $text);
        switch (true) {
            case $isIndex:
                $reference
                    ->setType(Reference::TYPE_INDEX)
                    ->setIndex(intval($text));
                break;

            case '-' === $text:
                $reference->setType(Reference::TYPE_NEXT_INDEX);
                break;

            default:
                $reference->setType(Reference::TYPE_PROPERTY);
        }
        $this
            ->locator
            ->addReference($reference);
    }
}
