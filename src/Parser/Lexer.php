<?php

namespace Remorhaz\JSONPointer\Parser;

/**
 * Lexical analyzer.
 *
 * @package JSONPointer
 */
class Lexer
{

    /**
     * @var Lexer\Scanner|null
     */
    protected $scanner;

    /**
     * @var Lexer\TokenBuffer
     */
    protected $buffer;


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
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function factory()
    {
        return new static();
    }


    /**
     * Sets source text.
     *
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this
            ->getScanner()
            ->setText($text);
        $this->buffer = null;
        return $this;
    }


    /**
     * Checks if there're no tokens to read.
     *
     * @return bool
     */
    public function isEnd()
    {
        return $this->getBuffer()->isEnd() && $this->getScanner()->isEnd();
    }


    /**
     * Reads next token from buffer.
     *
     * @return Token
     * @throws Lexer\UnknownSyntaxException
     */
    public function readToken()
    {
        if ($this->getBuffer()->isEnd()) {
            try {
                $token = $this
                    ->getScanner()
                    ->readToken();
            } catch (Lexer\Scanner\UnknownSyntaxException $e) {
                throw new Lexer\UnknownSyntaxException(
                    "Unknown syntax error near position #{$this->getNextPosition()}",
                    $this->getNextPosition(),
                    $e
                );
            }
            $this->assertErrorToken($token);
            $this
                ->getBuffer()
                ->addToken($token);
        }
        return $this
            ->getBuffer()
            ->readToken();
    }


    /**
     * Cancels reading of last token.
     *
     * @return $this
     */
    public function unreadToken()
    {
        $this
            ->getBuffer()
            ->unreadToken();
        return $this;
    }


    /**
     * Returns text scanner.
     *
     * @return Lexer\Scanner
     */
    protected function getScanner()
    {
        if (null === $this->scanner) {
            $this->scanner = Lexer\Scanner::factory();
        }
        return $this->scanner;
    }


    /**
     * Returns token buffer.
     *
     * @return Lexer\TokenBuffer
     */
    protected function getBuffer()
    {
        if (null === $this->buffer) {
            $this->buffer = Lexer\TokenBuffer::factory();
        }
        return $this->buffer;
    }


    /**
     * Returns position of a token that is not in buffer yet.
     *
     * @return int
     */
    protected function getNextPosition()
    {
        return $this
            ->getBuffer()
            ->getNextPosition();
    }


    /**
     * Checks if token is an error and processes it.
     *
     * @param Token $token
     * @return $this
     * @throws Lexer\SyntaxException
     * @throws Lexer\UnknownSyntaxException
     */
    protected function assertErrorToken(Token $token)
    {
        if (!$token->isError()) {
            return $this;
        }
        switch ($token->getType()) {
            case Token::TYPE_ERROR_INVALID_ESCAPE:
                throw new Lexer\SyntaxException(
                    "Invalid escape sequence at position #{$this->getNextPosition()}",
                    $this->getNextPosition()
                );

            default:
                throw new Lexer\UnknownSyntaxException(
                    "Unknown type of error token: {$token->getType()} at position #{$this->getNextPosition()}",
                    $this->getNextPosition()
                );
        }
    }
}
