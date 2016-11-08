<?php

namespace Remorhaz\JSON\Pointer;

use Remorhaz\JSON\Data\RawSelectableReader;
use Remorhaz\JSON\Data\RawSelectableWriter;
use Remorhaz\JSON\Data\SelectableReaderInterface;
use Remorhaz\JSON\Data\SelectableWriterInterface;
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
     * @var SelectableReaderInterface
     */
    protected $reader;

    /**
     * Parser object.
     *
     * @var Parser|null
     */
    protected $parser;


    /**
     * Constructor.
     *
     * @param SelectableReaderInterface $reader
     */
    public function __construct(SelectableReaderInterface $reader)
    {
        $this->reader = $reader;
    }


    public function test(string $text): bool
    {
        $this->getReader()->selectRoot();
        return (bool) (new OperationTest($this->getLocator($text), $this->getReader()))
            ->perform()
            ->getResult();
    }


    public function read(string $text)
    {
        $this->getReader()->selectRoot();
        return (new OperationRead($this->getLocator($text), $this->getReader()))
            ->perform()
            ->getResult();
    }


    public function add(string $text, $value)
    {
        $this->getReader()->selectRoot();
        $valueReader = new RawSelectableReader($value);
        (new OperationAdd($this->getLocator($text), $this->getWriter(), $valueReader))
            ->perform();
        return $this;
    }


    public function replace(string $text, $value)
    {
        $this->getReader()->selectRoot();
        $valueReader = new RawSelectableWriter($value);
        (new OperationReplace($this->getLocator($text), $this->getWriter(), $valueReader))
            ->perform();
        return $this;
    }


    public function remove($text)
    {
        $this->getReader()->selectRoot();
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
     * @return Locator
     */
    protected function getLocator(string $text): Locator
    {
        return $this
            ->getParser()
            ->setText($text)
            ->getLocator();
    }


    protected function getReader(): SelectableReaderInterface
    {
        return $this->reader;
    }


    protected function getWriter(): SelectableWriterInterface
    {
        if (!$this->reader instanceof SelectableWriterInterface) {
            throw new LogicException("Modifying data is not supported by data accessor");
        }
        return $this->reader;
    }
}
