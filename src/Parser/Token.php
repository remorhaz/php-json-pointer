<?php

namespace Remorhaz\JSONPointer\Parser;

use Remorhaz\JSONPointer\PregHelper;

/**
 * JSON Pointer token descriptor.
 */
class Token
{

    /**
     * Slash token: "/".
     */
    const TYPE_SLASH = 0x01;

    /**
     * Escaped token: "~" ( "0" / "1" ).
     */
    const TYPE_ESCAPED = 0x02;

    /**
     * Unescaped token: any valid Unicode symbol, excepting "/" and "~".
     */
    const TYPE_UNESCAPED = 0x03;

    /**
     * Error token: incomplete escape sequence "~".
     */
    const TYPE_ERROR_INVALID_ESCAPE = 0x04;

    /**
     * Token type (self::TYPE_*).
     *
     * @var int|null
     */
    protected $type;

    /**
     * Position of token's first symbol in source text.
     *
     * @var int|null
     */
    protected $position;

    /**
     * Text of token (part of original source text).
     *
     * @var string
     */
    protected $text;

    /**
     * Value of token.
     *
     * @var string
     */
    protected $value;

    /**
     * Length of token text (in Unicode symbols).
     *
     * @var int|null
     */
    protected $length;


    /**
     * Constructor.
     */
    protected function __construct()
    {
    }


    /**
     * Creates object instance.
     *
     * @param int|null $type
     * @param string|null $text
     * @param int|null $length
     * @param string|null $value
     * @return static
     */
    public static function factory($type = null, $text = null, $length = null, $value = null)
    {
        $token = new static();
        if (null !== $type) {
            $token->setType($type);
        }
        if (null !== $text) {
            $token->setText($text);
        }
        if (null !== $length) {
            $token->setLength($length);
        }
        if (null !== $value) {
            $token->setValue($value);
        }
        return $token;
    }


    /**
     * Returns token type (one of self::TYPE_* constants).
     *
     * @return int
     * @throws Token\LogicException
     */
    public function getType()
    {
        if (null === $this->type) {
            throw new Token\LogicException("Token type is not set");
        }
        return $this->type;
    }


    /**
     * Checks if token is a part of reference.
     *
     * @return bool
     */
    public function isReferencePart()
    {
        return
            $this->getType() == self::TYPE_ESCAPED ||
            $this->getType() == self::TYPE_UNESCAPED;
    }


    /**
     * Checks if token is a slash.
     *
     * @return bool
     */
    public function isSlash()
    {
        return $this->getType() == self::TYPE_SLASH;
    }


    /**
     * Checks if token is an error.
     *
     * @return bool
     */
    public function isError()
    {
        $errorTypeList = [
            self::TYPE_ERROR_INVALID_ESCAPE,
        ];
        return in_array($this->getType(), $errorTypeList);
    }


    /**
     * Returns position of token's first symbol in source string.
     *
     * @return int
     * @throws Token\LogicException
     */
    public function getPosition()
    {
        if (null === $this->position) {
            throw new Token\LogicException("Token position is not set");
        }
        return $this->position;
    }


    /**
     * Returns position of next token.
     *
     * @return int
     */
    public function getNextPosition()
    {
        return $this->getPosition() + $this->getLength();
    }


    /**
     * Returns token text.
     *
     * @return string
     * @throws Token\LogicException
     */
    public function getText()
    {
        if (null === $this->text) {
            throw new Token\LogicException("Token content is not set");
        }
        return $this->text;
    }


    /**
     * Sets token type (one of self::TYPE_* constants).
     *
     * @param int $type
     * @return $this
     * @throws Token\InvalidArgumentException
     * @throws Token\DomainException
     */
    public function setType($type)
    {
        if (!is_int($type)) {
            throw new Token\InvalidArgumentException("Token type must be an integer");
        }
        $typeList = [
            self::TYPE_SLASH,
            self::TYPE_ESCAPED,
            self::TYPE_UNESCAPED,
            self::TYPE_ERROR_INVALID_ESCAPE,
        ];
        if (!in_array($type, $typeList)) {
            throw new Token\DomainException("Invalid token type: {$type}");
        }
        $this->type = (int) $type;
        return $this;
    }


    /**
     * Sets position of token in source string.
     *
     * @param int $position
     * @return $this
     * @throws Token\InvalidArgumentException
     * @throws Token\DomainException
     */
    public function setPosition($position)
    {
        if (!is_int($position)) {
            throw new Token\InvalidArgumentException("Token position must be an integer");
        }
        if ($position < 0) {
            throw new Token\DomainException("Negative token position");
        }
        $this->position = $position;
        return $this;
    }


    /**
     * Sets token text.
     *
     * @param string $text
     * @return $this
     * @throws Token\InvalidArgumentException
     * @throws Token\LengthException
     */
    public function setText($text)
    {
        if (!is_string($text)) {
            throw new Token\InvalidArgumentException("Token text must be a string");
        }
        if (strlen($text) == 0) {
            throw new Token\LengthException("Empty token content");
        }
        $this->text = $text;
        return $this;
    }


    /**
     * Returns length of token text (in Unicode symbols).
     *
     * @return int
     */
    public function getLength()
    {
        if (null === $this->length) {
            throw new Token\LogicException("Token length is not set");
        }
        return $this->length;
    }


    public function setLength($length)
    {
        if (!is_int($length)) {
            throw new Token\InvalidArgumentException("Token length must be an integer");
        }
        if ($length < 0) {
            throw new Token\DomainException("Token length must be non-negative");
        }
        $this->length = $length;
        return $this;
    }


    public function getValue()
    {
        if (null === $this->value) {
            throw new Token\LogicException("Token value is not set");
        }
        return $this->value;
    }


    public function setValue($value)
    {
        if (!is_string($value)) {
            throw new Token\InvalidArgumentException("Value mast be a string");
        }
        $this->value = $value;
        return $this;
    }
}
