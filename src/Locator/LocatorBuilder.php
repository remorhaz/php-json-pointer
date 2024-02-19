<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Pointer\Locator;

use function array_map;
use function implode;
use function str_replace;

final class LocatorBuilder implements LocatorBuilderInterface
{
    private ?LocatorInterface $locator = null;

    /**
     * @var list<ReferenceInterface>
     */
    private array $references = [];

    public static function create(): LocatorBuilderInterface
    {
        return new self(new ReferenceFactory());
    }

    public function __construct(
        private readonly ReferenceFactoryInterface $referenceFactory,
    ) {
    }

    public function addReference(string $text): void
    {
        if (isset($this->locator)) {
            throw new Exception\LocatorAlreadyBuiltException();
        }

        $reference = $this
            ->referenceFactory
            ->createReference($text);
        $this->references[] = $reference;
    }

    public function getLocator(): LocatorInterface
    {
        return $this->locator ??= new Locator(...$this->references);
    }

    public function export(): string
    {
        $references = $this
            ->getLocator()
            ->references();

        return empty($references)
            ? ''
            : '/' . implode('/', array_map([$this, 'escapeReference'], $references));
    }

    private function escapeReference(ListedReferenceInterface $reference): string
    {
        $text = $reference
            ->getReference()
            ->getPropertyName();

        return str_replace(['~', '/'], ['~0', '~1'], $text);
    }
}
