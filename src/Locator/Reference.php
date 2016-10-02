<?php

namespace Remorhaz\JSONPointer\Locator;

/**
 * Single reference of locator.
 *
 * @package JSONPointer
 */
class Reference
{

    /**
     * Reference is an array index (decimal digit with no leading zeros).
     */
    const TYPE_INDEX = 0x01;

    /**
     * Reference is a non-existing array index (single "-" symbol).
     */
    const TYPE_NEXT_INDEX = 0x02;

    /**
     * Reference is an object property (any other string).
     */
    const TYPE_PROPERTY = 0x03;

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
     * Constructor.
     */
    protected function __construct()
    {
    }


    /**
     * Creates object instance.
     *
     * @return static
     */
    public static function factory()
    {
        return new static();
    }


    /**
     * Returns reference type.
     *
     * @return int
     * @throws LogicException
     */
    public function getType()
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
     * @throws InvalidArgumentException
     */
    public function setType($type)
    {
        if (!is_int($type)) {
            throw new InvalidArgumentException("Reference type must be an integer");
        }
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
     * @param string|int $key
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setKey($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException("Reference key must be string");
        }
        $this->key = $key;
        return $this;
    }


    public function isLast()
    {
        return !$this
            ->getLocator()
            ->hasReference($this->getIndex() + 1);
    }


    public function isFirst()
    {
        return !$this
            ->getLocator()
            ->hasReference($this->getIndex() - 1);
    }


    /**
     * @return Reference
     */
    public function getPrevious()
    {
        return $this
            ->getLocator()
            ->getReference($this->getIndex() - 1);
    }


    /**
     * @return int
     * @throws LogicException
     */
    public function getLength()
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
    public function setLength($length)
    {
        if (!is_int($length)) {
            throw new InvalidArgumentException("Reference length must be an integer");
        }
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
    public function getPosition()
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
    public function setPosition($position)
    {
        if (!is_int($position)) {
            throw new InvalidArgumentException("Reference position must be an integer");
        }
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
    public function getPath()
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
    public function getText()
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
    public function setText($text)
    {
        if (!is_string($text)) {
            throw new InvalidArgumentException("Reference text must be a string");
        }
        $this->text = $text;
        return $this;
    }


    /**
     * @param int $index
     * @return $this
     */
    public function setIndex($index)
    {
        if (!is_int($index)) {
            throw new InvalidArgumentException("Reference index must be an integer");
        }
        if ($index < 0) {
            throw new DomainException("Reference index must be non-negative");
        }
        $this->index = $index;
        return $this;

    }


    /**
     * @return int
     */
    public function getIndex()
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
    public function getLocator()
    {
        if (null === $this->locator) {
            throw new LogicException("Locator is not set");
        }
        return $this->locator;
    }
}
