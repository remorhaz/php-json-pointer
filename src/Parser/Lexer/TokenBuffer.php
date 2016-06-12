<?php

namespace Remorhaz\JSONPointer\Parser\Lexer;

use Remorhaz\JSONPointer\Parser\Token;

/**
 * Stores scanned tokens.
 *
 * @package JSONPointer
 */
class TokenBuffer
{

    /**
     * List of tokens that have been read from source.
     *
     * @var Token[]
     */
    protected $tokenList = [];

    /**
     * Marks current position in token list.
     *
     * @var int
     */
    protected $cursor = 0;


    /**
     * Constructor.
     */
    protected function __construct()
    {
    }


    /**
     * Creates instance of object.
     *
     * @return static
     */
    public static function factory()
    {
        return new static();
    }


    /**
     * Checks if internal cursor points at the end of buffer.
     *
     * @return bool
     */
    public function isEnd()
    {
        return count($this->tokenList) == $this->cursor;
    }


    public function addToken(Token $token)
    {
        $position = $this->getNextPosition();
        $this->tokenList[] = $token->setPosition($position);
        return $this;
    }


    public function getNextPosition()
    {
        return empty($this->tokenList)
            ? 0
            : $this
                ->getLastToken()
                ->getNextPosition();
    }


    /**
     * Reads token from current cursor position and advances cursor.
     *
     * @return Token
     * @throws TokenBuffer\LogicException
     */
    public function readToken()
    {
        if ($this->isEnd()) {
            throw new TokenBuffer\LogicException("The end of buffer is reached");
        }
        $token = $this->getTokenAtIndex($this->cursor);
        $this->cursor++;
        return $token;
    }


    /**
     * Cancels reading of last token.
     *
     * @return $this
     * @throws TokenBuffer\LogicException
     */
    public function unreadToken()
    {
        if (0 == $this->cursor) {
            throw new TokenBuffer\LogicException("The beginning of buffer is reached");
        }
        $this->cursor--;
        return $this;
    }


    /**
     * @return Token
     */
    protected function getLastToken()
    {
        $lastIndex = count($this->tokenList) - 1;
        return $this->getTokenAtIndex($lastIndex);
    }


    /**
     * Returns token at given index.
     *
     * @param int $index
     * @return Token
     * @throws TokenBuffer\OutOfRangeException
     */
    protected function getTokenAtIndex($index)
    {
        if (!$this->hasTokenAtIndex($index)) {
            throw new TokenBuffer\OutOfRangeException("No token in buffer at given index");
        }
        return $this->tokenList[$index];
    }


    /**
     * Checks if token exists at given index.
     *
     * @param int $index
     * @return bool
     */
    protected function hasTokenAtIndex($index)
    {
        return isset($this->tokenList[$index]);
    }
}
