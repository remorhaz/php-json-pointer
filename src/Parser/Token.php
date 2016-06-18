<?php

namespace Remorhaz\JSONPointer\Parser;

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
     */
    public function getType()
    {
        if (null === $this->type) {
            throw new LogicException("Token type is not set");
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
     */
    public function getPosition()
    {
        if (null === $this->position) {
            throw new LogicException("Token position is not set");
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
     */
    public function getText()
    {
        if (null === $this->text) {
            throw new LogicException("Token content is not set");
        }
        return $this->text;
    }


    /**
     * Sets token type (one of self::TYPE_* constants).
     *
     * @param int $type
     * @return $this
     */
    public function setType($type)
    {
        if (!is_int($type)) {
            throw new InvalidArgumentException("Token type must be an integer");
        }
        $typeList = [
            self::TYPE_SLASH,
            self::TYPE_ESCAPED,
            self::TYPE_UNESCAPED,
            self::TYPE_ERROR_INVALID_ESCAPE,
        ];
        if (!in_array($type, $typeList)) {
            throw new DomainException("Invalid token type: {$type}");
        }
        $this->type = (int) $type;
        return $this;
    }


    /**
     * Sets position of token in source string.
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        if (!is_int($position)) {
            throw new InvalidArgumentException("Token position must be an integer");
        }
        if ($position < 0) {
            throw new DomainException("Negative token position");
        }
        $this->position = $position;
        return $this;
    }


    /**
     * Sets token text.
     *
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        if (!is_string($text)) {
            throw new InvalidArgumentException("Token text must be a string");
        }
        if (strlen($text) == 0) {
            throw new LengthException("Empty token content");
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
            throw new LogicException("Token length is not set");
        }
        return $this->length;
    }


    public function setLength($length)
    {
        if (!is_int($length)) {
            throw new InvalidArgumentException("Token length must be an integer");
        }
        if ($length < 0) {
            throw new DomainException("Token length must be non-negative");
        }
        $this->length = $length;
        return $this;
    }


    public function getValue()
    {
        if (null === $this->value) {
            throw new LogicException("Token value is not set");
        }
        return $this->value;
    }


    public function setValue($value)
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException("Value mast be a string");
        }
        $this->value = $value;
        return $this;
    }
}
