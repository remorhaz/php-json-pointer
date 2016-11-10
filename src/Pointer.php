<?php

namespace Remorhaz\JSON\Pointer;

use Remorhaz\JSON\Data\ReaderInterface;
use Remorhaz\JSON\Data\SelectorInterface;
use Remorhaz\JSON\Data\WriterInterface;
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
     * Data reader.
     *
     * @var SelectorInterface
     */
    protected $selector;

    /**
     * Parser object.
     *
     * @var Parser|null
     */
    protected $parser;


    /**
     * Constructor.
     *
     * @param SelectorInterface $selector
     */
    public function __construct(SelectorInterface $selector)
    {
        $this->selector = $selector;
    }


    public function test(string $text): bool
    {
        $this->getSelector()->selectRoot();
        return (bool) (new OperationTest($this->getLocator($text), $this->getSelector()))
            ->perform()
            ->getResult()
            ->getAsStruct();
    }


    public function read(string $text): ReaderInterface
    {
        $this->getSelector()->selectRoot();
        return (new OperationRead($this->getLocator($text), $this->getSelector()))
            ->perform()
            ->getResult();
    }


    public function add(string $text, ReaderInterface $valueReader)
    {
        $this->getSelector()->selectRoot();
        (new OperationAdd($this->getLocator($text), $this->getWriter(), $valueReader))
            ->perform();
        return $this;
    }


    public function replace(string $text, ReaderInterface $valueReader)
    {
        $this->getSelector()->selectRoot();
        (new OperationReplace($this->getLocator($text), $this->getWriter(), $valueReader))
            ->perform();
        return $this;
    }


    public function remove($text)
    {
        $this->getSelector()->selectRoot();
        (new OperationRemove($this->getLocator($text), $this->getWriter()))
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
     * @param string $text
     * @return Locator
     */
    protected function getLocator(string $text): Locator
    {
        return $this
            ->getParser()
            ->setText($text)
            ->getLocator();
    }


    protected function getSelector(): SelectorInterface
    {
        return $this->selector;
    }


    protected function getWriter(): WriterInterface
    {
        if (!$this->selector instanceof WriterInterface) {
            throw new LogicException("Can't modify data with read-only selector");
        }
        return $this->selector;
    }
}
