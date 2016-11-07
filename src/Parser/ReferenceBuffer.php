<?php

namespace Remorhaz\JSON\Pointer\Parser;

use Remorhaz\JSON\Pointer\Locator\Reference;

class ReferenceBuffer
{

    /**
     * @var Reference|null
     */
    protected $reference;

    protected $value = '';

    protected $length = 0;

    protected $text = '';


    protected function __construct()
    {
    }


    /**
     * @return static
     */
    public static function factory()
    {
        return new static();
    }


    public function addToken(Token $token)
    {
        if (null !== $this->reference) {
            throw new LogicException("Token buffer was not reset before adding tokens");
        }
        $this->value .= $token->getValue();
        $this->text .= $token->getText();
        $this->length += $token->getLength();
        return $this;
    }


    public function flush()
    {
        if (null !== $this->reference) {
            throw new LogicException("Reference already flushed in token buffer");
        }
        $this->reference = Reference::factory()
            ->setLength($this->length)
            ->setKey($this->value)
            ->setText($this->text);
        return $this->resetBuffer();
    }


    /**
     * @return Reference
     */
    public function getReference(): Reference
    {
        if (null === $this->reference) {
            throw new LogicException("Reference is not set in token buffer");
        }
        return $this->reference;
    }


    public function reset()
    {
        $this->reference = null;
        return $this->resetBuffer();
    }


    protected function resetBuffer()
    {
        $this->value = '';
        $this->text = '';
        $this->length = 0;
        return $this;
    }
}
