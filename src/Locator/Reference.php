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
    protected $type;

    /**
     * Reference value.
     *
     * @var string|null
     */
    protected $value;

    /**
     * Flag of last reference in locator.
     *
     * @var bool|null
     */
    protected $isLast;

    /**
     * Sum of token lengths.
     *
     * @var int|null
     */
    protected $length;

    /**
     * Reference position (Unicode symbol offset).
     *
     * @var int|null
     */
    protected $position;

    /**
     * Reference path (part of JSON Pointer from start to the reference inclusively).
     *
     * @var string|null
     */
    protected $path;

    /**
     * @var string|null
     */
    protected $text;

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
     * @throws Reference\LogicException
     */
    public function getType()
    {
        if (null === $this->type) {
            throw new Reference\LogicException("Reference type is not set");
        }
        return $this->type;
    }


    /**
     * Sets reference type.
     *
     * @param int $type
     * @return $this
     * @throws Reference\DomainException
     * @throws Reference\InvalidArgumentException
     */
    public function setType($type)
    {
        if (!is_int($type)) {
            throw new Reference\InvalidArgumentException("Reference type must be an integer");
        }
        $typeList = [
            self::TYPE_INDEX,
            self::TYPE_NEXT_INDEX,
            self::TYPE_PROPERTY,
        ];
        if (!in_array($type, $typeList)) {
            throw new Reference\DomainException("Invalid reference type: {$type}");
        }
        $this->type = (int) $type;
        return $this;
    }


    /**
     * Returns reference value.
     *
     * @return int|string
     * @throws Reference\LogicException
     */
    public function getValue()
    {
        if (null === $this->value) {
            throw new Reference\LogicException("Reference value is not set");
        }
        return $this->value;
    }


    /**
     * Sets reference value.
     *
     * @param string|int $value
     * @return $this
     * @throws Reference\InvalidArgumentException
     */
    public function setValue($value)
    {
        if (!is_string($value)) {
            throw new Reference\InvalidArgumentException("Reference value must be string");
        }
        $this->value = $value;
        return $this;
    }


    public function isLast()
    {
        if (null === $this->isLast) {
            throw new Reference\LogicException("Flag of last reference is not set");
        }
        return $this->isLast;
    }


    public function setIsLast($isLast = true)
    {
        if (!is_bool($isLast)) {
            throw new Reference\InvalidArgumentException("Reference value must be boolean");
        }
        $this->isLast = $isLast;
        return $this;
    }
    
    
    public function markAsLast()
    {
        $this->isLast = true;
        return $this;
    }
    
    
    public function markAsNotLast()
    {
        $this->isLast = false;
        return $this;
    }


    /**
     * @return int
     * @throws Reference\LogicException
     */
    public function getLength()
    {
        if (null === $this->length) {
            throw new Reference\LogicException("Reference length is not set");
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
            throw new Reference\InvalidArgumentException("Reference length must be an integer");
        }
        if ($length < 0) {
            throw new Reference\DomainException("Reference length must be positive");
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
            throw new Reference\LogicException("Reference position is not set");
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
            throw new Reference\InvalidArgumentException("Reference position must be an integer");
        }
        if ($position < 0) {
            throw new Reference\DomainException("Reference position must be non-negative");
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
        if (null === $this->path) {
            throw new Reference\LogicException("Reference path is not set");
        }
        return $this->path;
    }


    /**
     * Sets reference path.
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        if (!is_string($path)) {
            throw new Reference\InvalidArgumentException("Reference path must be a string");
        }
        $this->path = $path;
        return $this;
    }


    /**
     * @return string
     */
    public function getText()
    {
        if (null === $this->text) {
            throw new reference\LogicException("Reference text is not set");
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
            throw new Reference\InvalidArgumentException("Reference text must be a string");
        }
        $this->text = $text;
        return $this;
    }
}
