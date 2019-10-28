<?php

namespace Remorhaz\JSON\Pointer\Parser;

use Remorhaz\JSON\Data\Path\Path;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Pointer\Locator\LocatorInterface;
use Remorhaz\JSON\Pointer\Parser\Lexer\Lexer;
use Remorhaz\JSON\Pointer\Locator\Locator;
use Remorhaz\JSON\Pointer\Locator\Reference;

class Parser implements ParserInterface
{

    /**
     * FSM state expecting part of a pointer:  "/" reference.
     */
    const STATE_POINTER_PART    = 0;

    /**
     * FSM state expecting part of a reference: *( escaped / unescaped).
     */
    const STATE_REFERENCE_PART  = 1;

    /**
     * FSM pseudo-state for successful finish.
     */
    const STATE_FINISH          = 2;

    /**
     * ReferenceBuffer for a single reference.
     *
     * @var ReferenceBuffer
     */
    protected $referenceBuffer;

    /**
     * Temporary buffer for partially built locator object.
     * @var Locator
     */
    protected $locatorBuffer;

    /**
     * Fully built locator object.
     *
     * @var Locator
     */
    protected $locator;

    /**
     * Lexical analyzer.
     *
     * @var Lexer|null
     */
    protected $lexer;


    /**
     * Constructor.
     */
    public function __construct()
    {
    }


    /**
     * Creates object instance.
     *
     * @return static
     */
    public static function factory(): self
    {
        return new static();
    }


    /**
     * Sets source text.
     *
     * @param string $text
     * @return $this
     */
    public function setText(string $text): ParserInterface
    {
        $this
            ->getLexer()
            ->setText($text);
        $this->locator = null;
        return $this;
    }


    /**
     * Returns built locator.
     *
     * @return Locator
     */
    public function getLocator(): LocatorInterface
    {
        if (null === $this->locator) {
            $this->buildLocator();
        }
        return $this->locator;
    }


    /**
     * Builds locator from source using finite state machine.
     *
     * @return $this
     * @throws DomainException
     */
    protected function buildLocator(): self
    {
        $this
            ->resetLocatorBuffer()
            ->getReferenceBuffer()
            ->reset();
        $state = self::STATE_POINTER_PART;
        while (true) {
            switch ($state) {
                case self::STATE_POINTER_PART:
                    // Expecting part of a pointer or end of text.
                    $state = $this->processPointerPartState();
                    break;

                case self::STATE_REFERENCE_PART:
                    // Expecting reference token part.
                    $state = $this->processReferencePartState();
                    break;

                case self::STATE_FINISH:
                    // End of text is found, parsing is finished successfully.
                    break 2;

                default:
                    throw new DomainException("Invalid parser state: {$state}");
            }
        }
        $this->locator = $this->getLocatorBuffer();
        return $this
            ->resetLocatorBuffer();
    }


    /**
     * Expecting part of a pointer or end of text.
     *
     * @return int
     * @throws SyntaxException
     */
    protected function processPointerPartState(): int
    {
        $lexer = $this->getLexer();
        if ($lexer->isEnd()) {
            // End of text is found, expecting successful finish.
            return self::STATE_FINISH;
        }
        $token = $lexer->readToken();
        if ($token->isSlash()) {
            // Slash found, expecting reference token part
            return self::STATE_REFERENCE_PART;
        }
        throw new SyntaxException(
            "Symbol '/' expected at position #{$token->getPosition()}",
            $token->getPosition()
        );
    }


    /**
     * Expecting reference token part.
     *
     * @return int
     */
    protected function processReferencePartState(): int
    {
        $lexer = $this->getLexer();
        if ($lexer->isEnd()) {
            // Text is finished and so does current reference.
            $this->flushReferenceBuffer();
            return self::STATE_FINISH;
        }
        $token = $lexer->readToken();
        if ($token->isReferencePart()) {
            // Putting token to buffer and continue.
            $this
                ->getReferenceBuffer()
                ->addToken($token);
            return self::STATE_REFERENCE_PART;
        }
        // Token doesn't belong to current reference, so it's finished on previous token.
        $this->flushReferenceBuffer();
        $lexer->unreadToken();
        return self::STATE_POINTER_PART; // Expecting next part of a pointer
    }


    protected function flushReferenceBuffer(): self
    {
        $reference = $this
            ->getReferenceBuffer()
            ->flush()
            ->getReference();
        $this
            ->setupReferenceType($reference)
            ->getReferenceBuffer()
            ->reset();
        $this
            ->getLocatorBuffer()
            ->addReference($reference);
        return $this;
    }


    protected function setupReferenceType(Reference $reference): self
    {
        $result = preg_match('#^(0|[1-9]\d*)$#u', $reference->getKey());
        PregHelper::assertMatchResult(
            $result,
            RegExpException::class,
            "Regular expression error on reference type detection"
        );
        if (1 === $result) {
            $type = Reference::TYPE_INDEX;
        } elseif ('-' == $reference->getKey()) {
            $type = Reference::TYPE_NEXT_INDEX;
        } else {
            $type = Reference::TYPE_PROPERTY;
        }
        $reference
            ->setType($type);
        return $this;
    }


    /**
     * Returns lexical analyzer.
     *
     * @return Lexer
     */
    protected function getLexer(): Lexer
    {
        if (null === $this->lexer) {
            $this->lexer = Lexer::factory();
        }
        return $this->lexer;
    }


    /**
     * @return ReferenceBuffer
     */
    protected function getReferenceBuffer(): ReferenceBuffer
    {
        if (null === $this->referenceBuffer) {
            $this->referenceBuffer = ReferenceBuffer::factory();
        }
        return $this->referenceBuffer;
    }


    /**
     * @return Locator
     */
    protected function getLocatorBuffer(): Locator
    {
        if (null === $this->locatorBuffer) {
            $this->locatorBuffer = Locator::factory();
        }
        return $this->locatorBuffer;
    }


    /**
     * @return $this
     */
    protected function resetLocatorBuffer(): self
    {
        $this->locatorBuffer = null;
        return $this;
    }
}
