<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

final class LocatorBuilder implements LocatorBuilderInterface
{

    private $locator;

    private $referenceFactory;

    private $references = [];

    public static function create(): LocatorBuilderInterface
    {
        return new self(new ReferenceFactory);
    }

    public function __construct(ReferenceFactoryInterface $referenceFactory)
    {
        $this->referenceFactory = $referenceFactory;
    }

    public function addReference(string $text): void
    {
        if (isset($this->locator)) {
            throw new Exception\LocatorAlreadyBuiltException;
        }

        $reference = $this
            ->referenceFactory
            ->createReference($text);
        $this->references[] = $reference;
    }

    public function getLocator(): LocatorInterface
    {
        if (!isset($this->locator)) {
            $this->locator = new Locator(...$this->references);
        }

        return $this->locator;
    }
}
