<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

use function array_map;
use function implode;
use function str_replace;

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

    public function export(): string
    {
        $references = $this
            ->getLocator()
            ->references();

        if (empty($references)) {
            return '';
        }

        return '/' . implode('/', array_map([$this, 'escapeReference'], $references));
    }

    private function escapeReference(ListedReferenceInterface $reference): string
    {
        $text = $reference
            ->getReference()
            ->getPropertyName();

        return str_replace(['~', '/'], ['~0', '~1'], $text);
    }
}
