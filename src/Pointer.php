<?php

namespace Remorhaz\JSON\Pointer;

use Remorhaz\JSON\Data\RawWriter;
use Remorhaz\JSON\Pointer\Evaluator\OperationAdd;
use Remorhaz\JSON\Pointer\Evaluator\OperationRemove;
use Remorhaz\JSON\Pointer\Evaluator\OperationRead;
use Remorhaz\JSON\Pointer\Evaluator\OperationReplace;
use Remorhaz\JSON\Pointer\Evaluator\OperationTest;
use Remorhaz\JSON\Pointer\Locator\Locator;
use Remorhaz\JSON\Pointer\Parser\Parser;

class Pointer
{

    /**
     * JSON Pointer string buffer.
     *
     * @var string|null
     */
    protected $text;

    /**
     * Data for evaluation.
     *
     * @var mixed
     */
    protected $data;

    /**
     * Internal flag of data being set.
     *
     * @var bool
     */
    protected $isDataSet = false;

    /**
     * Locator buffer.
     *
     * @var Locator|null
     */
    protected $locator;

    /**
     * Parser object.
     *
     * @var Parser|null
     */
    protected $parser;


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
     * Sets JSON Pointer string.
     *
     * @param string $text
     * @return $this
     */
    public function setText(string $text)
    {
        if ($this->text !== $text) {
            $this->text = $text;
            $this->locator = null;
        }
        return $this;
    }


    /**
     * Returns JSON Pointer string.
     *
     * @return string
     * @throws LogicException
     */
    public function getText(): string
    {
        if (null === $this->text) {
            throw new LogicException("JSON Pointer text is not set");
        }
        return $this->text;
    }


    public function setData(&$data)
    {
        $this->data = &$data;
        $this->isDataSet = true;
        return $this;
    }


    protected function &getData()
    {
        if (!$this->isDataSet) {
            throw new LogicException("Data for evaluation is not set");
        }
        return $this->data;
    }


    public function test(): bool
    {
        $reader = new RawWriter($this->getData());
        return (bool) (new OperationTest($this->getLocator(), $reader))
            ->perform()
            ->getResult();
    }


    public function read()
    {
        $reader = new RawWriter($this->getData());
        return (new OperationRead($this->getLocator(), $reader))
            ->perform()
            ->getResult();
    }


    public function add($value)
    {
        $writer = new RawWriter($this->getData());
        $valueReader = new RawWriter($value);
        (new OperationAdd($this->getLocator(), $writer, $valueReader))
            ->perform();
        return $this;
    }


    public function replace($value)
    {
        $writer = new RawWriter($this->getData());
        $valueReader = new RawWriter($value);
        (new OperationReplace($this->getLocator(), $writer, $valueReader))
            ->perform();
        return $this;
    }


    public function remove()
    {
        $writer = new RawWriter($this->getData());
        (new OperationRemove($this->getLocator(), $writer))
            ->perform();
        return $this;
    }


    /**
     * Returns parser object.
     *
     * @return Parser
     */
    protected function getParser(): Parser
    {
        if (null === $this->parser) {
            $this->parser = Parser::factory();
        }
        return $this->parser;
    }


    /**
     * Returns locator built from source text.
     *
     * @return Locator
     */
    protected function getLocator(): Locator
    {
        if (null === $this->locator) {
            $this->locator = $this
                ->getParser()
                ->setText($this->getText())
                ->getLocator();
        }
        return $this->locator;
    }
}
