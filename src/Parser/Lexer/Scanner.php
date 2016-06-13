<?php

namespace Remorhaz\JSONPointer\Parser\Lexer;

use Remorhaz\JSONPointer\Parser\Token;
use Remorhaz\JSONPointer\Parser\PregHelper;

/**
 * Lexical analyzer's text scanner. It reads tokens directly from text string.
 *
 * @package JSONPointer
 */
class Scanner
{

    /**
     * Source text.
     *
     * @var string|null
     */
    protected $text;

    /**
     * ReferenceBuffer for part of source text that starts at current cursor position and is not shorter
     * than longest possible token.
     *
     * @var string|null
     */
    protected $textAtCursor;

    /**
     * Byte offset of next token to read.
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
     * Creates object instance.
     *
     * @return static
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function factory()
    {
        return new static();
    }


    /**
     * Returns source text.
     *
     * @return string
     * @throws Scanner\RuntimeException
     */
    public function getText()
    {
        if (null === $this->text) {
            throw new Scanner\RuntimeException("Text is not set");
        }
        return $this->text;
    }


    /**
     * Sets source text.
     *
     * @param string $text
     * @return $this
     * @throws Scanner\RegExpException
     */
    public function setText($text)
    {
        PregHelper::assertValidUTF8(
            $text,
            Scanner\RegExpException::class,
            "Regular expression error on checking source text"
        );
        $this->text = (string) $text;
        $this->textAtCursor = null;
        $this->cursor = 0;
        return $this;
    }


    /**
     * Checks if end of source text reached.
     *
     * @return bool
     */
    public function isEnd()
    {
        return strlen($this->getText()) == $this->cursor;
    }


    /**
     * Reads next token from text.
     *
     * @return Token
     * @throws Scanner/UnknownSyntaxException
     */
    public function readToken()
    {
        // List of types is reverse-sorted by frequency for typical locator.
        $typeList = [
            Token::TYPE_UNESCAPED,
            Token::TYPE_SLASH,
            Token::TYPE_ESCAPED,
        ];
        $token = $this->matchNextToken($typeList);
        if (null === $token) {
            // Checking for known syntax errors.
            $errorTypeList = [
                Token::TYPE_ERROR_INVALID_ESCAPE,
            ];
            $token = $this->matchNextToken($errorTypeList);
        }
        if (null === $token) {
            throw new Scanner\UnknownSyntaxException("Unknown syntax error in source text");
        }
        return $token;
    }


    /**
     * @param int[] $typeList
     * @return Token|null
     */
    protected function matchNextToken(array $typeList)
    {
        $token = null;
        foreach ($typeList as $type) {
            $text = $this->matchTokenTextAtCursor($type);
            if (null !== $text) {
                $token = Token::factory($type, $text, $this->getTextLength($text));
                switch ($type) {
                    case Token::TYPE_ESCAPED:
                        $token->setValue($this->unescape($text));
                        break;

                    case Token::TYPE_UNESCAPED:
                        $token->setValue($text);
                        break;
                }
                $this->advanceCursor(strlen($text));
                break;
            }
        }
        return $token;
    }


    /**
     * Advances cursor by given number of bytes.
     *
     * @param int $byteCount
     * @return $this
     * @throws Scanner\LogicException
     * @throws Scanner\DomainException
     */
    protected function advanceCursor($byteCount)
    {
        if (0 >= $byteCount) {
            throw new Scanner\DomainException("Byte count to advance cursor must be positive");
        }
        if ($this->isEnd()) {
            throw new Scanner\LogicException("Cursor is at the end of text and cannot be advanced");
        }
        $maxByteCount = strlen($this->getText()) - $this->cursor;
        if ($byteCount > $maxByteCount) {
            throw new Scanner\LogicException("Cursor cannot be advanced beyond the end of text");
        }
        $this->cursor += $byteCount;
        $this->textAtCursor = null;
        return $this;
    }


    protected function getTextLength($text)
    {
        $length = preg_match_all('#.#us', $text);
        PregHelper::assertMatchResult(
            $length,
            Scanner\RegExpException::class,
            "Regular expression error on getting token length"
        );
        return $length;
    }


    /**
     * Returns anchored regular expression pattern for given token type.
     *
     * @param int $type
     * @return string
     * @throws Scanner\DomainException
     */
    protected function getTokenPattern($type)
    {
        $patternMap = [
            Token::TYPE_SLASH => '/',
            Token::TYPE_ESCAPED => '~(0|1)',
            Token::TYPE_UNESCAPED => '[^/~]',
            Token::TYPE_ERROR_INVALID_ESCAPE => '~',
        ];
        if (!isset($patternMap[$type])) {
            throw new Scanner\DomainException("Unknown token type: {$type}");
        }
        $pattern = $patternMap[$type];
        return "#{$pattern}#uA";
    }


    /**
     * Returns part of source string that starts at current cursor position.
     *
     * @return string
     * @throws Scanner\LengthException
     * @todo Limit substring size with maximal token length (without breaking UTF-8).
     */
    protected function getTextAtCursor()
    {
        if (null === $this->textAtCursor) {
            $textAtCursor = substr($this->getText(), $this->cursor);
            if (strlen($textAtCursor) == 0) {
                throw new Scanner\LengthException("No more text to match tokens");
            }
            $this->textAtCursor = $textAtCursor;
        }
        return $this->textAtCursor;
    }


    /**
     * Checks if text at current cursor position starts from token of given type.
     *
     * @param int $type
     * @return string|null Token text or NULL.
     * @throws Scanner\LengthException
     * @throws Scanner\RegExpException
     */
    protected function matchTokenTextAtCursor($type)
    {
        $result = preg_match($this->getTokenPattern($type), $this->getTextAtCursor(), $matches);
        PregHelper::assertMatchResult(
            $result,
            Scanner\RegExpException::class,
            "Failed to match token text due to regular expression error"
        );
        if (1 === $result) {
            $text = $matches[0];
            if (strlen($text) == 0) {
                throw new Scanner\LengthException("Matched token text is empty");
            }
        } else {
            $text = null;
        }
        return $text;
    }


    protected function unescape($escapedText)
    {
        $unescapeMap = [
            '~0' => '~',
            '~1' => '/',
        ];
        if (!isset($unescapeMap[$escapedText])) {
            throw new Scanner\DomainException("Unknown escape sequence: {$escapedText}");
        }
        return $unescapeMap[$escapedText];
    }
}
