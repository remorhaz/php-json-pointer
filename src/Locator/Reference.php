<?php

namespace Remorhaz\JSON\Pointer\Locator;

/**
 * Single reference of locator.
 */
class Reference implements ReferenceInterface
{

    /**
     * Reference type (self::TYPE_*).
     *
     * @var int|null
     */
    private $type;

    /**
     * Reference value.
     *
     * @var string|null
     */
    private $key;

    /**
     * Sum of token lengths.
     *
     * @var int|null
     */
    private $length;

    /**
     * Reference position (Unicode symbol offset).
     *
     * @var int|null
     */
    private $position;

    /**
     * @var string|null
     */
    private $text;

    /**
     * @var int|null
     */
    private $index;

    /**
     * @var Locator|null
     */
    private $locator;

    /**
     * Returns reference type.
     *
     * @return int
     * @throws LogicException
     */
    public function getType(): int
    {
        if (null === $this->type) {
            throw new LogicException("Reference type is not set");
        }

        return $this->type;
    }

    /**
     * Sets reference type.
     *
     * @param int $type
     * @return $this
     * @throws DomainException
     */
    public function setType(int $type)
    {
        $typeList = [
            self::TYPE_INDEX,
            self::TYPE_NEXT_INDEX,
            self::TYPE_PROPERTY,
        ];
        if (!in_array($type, $typeList)) {
            throw new DomainException("Invalid reference type: {$type}");
        }
        $this->type = (int) $type;

        return $this;
    }

    /**
     * Returns reference value.
     *
     * @return int|string
     * @throws LogicException
     */
    public function getKey()
    {
        if (null === $this->key) {
            throw new LogicException("Reference key is not set");
        }

        return $this->key;
    }

    /**
     * Sets reference value.
     *
     * @param string $key
     * @return $this
     */
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }

    public function isLast(): bool
    {
        return !$this
            ->getLocator()
            ->hasReference($this->getIndex() + 1);
    }

    public function isFirst(): bool
    {
        return !$this
            ->getLocator()
            ->hasReference($this->getIndex() - 1);
    }

    /**
     * @return Reference
     */
    public function getPrevious(): Reference
    {
        return $this
            ->getLocator()
            ->getReference($this->getIndex() - 1);
    }

    /**
     * @return int
     * @throws LogicException
     */
    public function getLength(): int
    {
        if (null === $this->length) {
            throw new LogicException("Reference length is not set");
        }

        return $this->length;
    }

    /**
     * @param int $length
     * @return $this
     */
    public function setLength(int $length)
    {
        if ($length < 0) {
            throw new DomainException("Reference length must be positive");
        }
        $this->length = $length;

        return $this;
    }

    /**
     * Returns reference position in string (Unicode symbol offest).
     *
     * @return int
     */
    public function getPosition(): int
    {
        if (null === $this->position) {
            throw new LogicException("Reference position is not set");
        }

        return $this->position;
    }

    /**
     * Sets reference position.
     *
     * @param int $position
     * @return $this
     */
    public function setPosition(int $position)
    {
        if ($position < 0) {
            throw new DomainException("Reference position must be non-negative");
        }
        $this->position = $position;

        return $this;
    }

    /**
     * Returns reference path.
     *
     * @return string
     */
    public function getPath(): string
    {
        $prefix = $this->isFirst()
            ? ''
            : $this
                ->getPrevious()
                ->getPath();

        return "{$prefix}/{$this->getText()}";
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        if (null === $this->text) {
            throw new LogicException("Reference text is not set");
        }

        return $this->text;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param int $index
     * @return $this
     */
    public function setIndex(int $index)
    {
        if ($index < 0) {
            throw new DomainException("Reference index must be non-negative");
        }
        $this->index = $index;

        return $this;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        if (null === $this->index) {
            throw new LogicException("Reference index is not set");
        }

        return $this->index;
    }

    /**
     * @param Locator $locator
     * @return $this
     */
    public function setLocator(Locator $locator)
    {
        $this->locator = $locator;

        return $this;
    }

    /**
     * @return Locator
     */
    public function getLocator(): Locator
    {
        if (null === $this->locator) {
            throw new LogicException("Locator is not set");
        }

        return $this->locator;
    }
}
