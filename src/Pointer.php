<?php

namespace Remorhaz\JSONPointer;

use Remorhaz\JSONPointer\Evaluator\LocatorEvaluatorRead;
use Remorhaz\JSONPointer\Evaluator\LocatorEvaluatorTest;
use Remorhaz\JSONPointer\Evaluator\LocatorEvaluatorWrite;
use Remorhaz\JSONPointer\Locator\Locator;
use Remorhaz\JSONPointer\Parser\Parser;

class Pointer
{

    /**
     * By default, RFC-6901 allows addressing only numeric indices in array,
     * because JSON arrays can have only numeric indices. But PHP arrays can
     * have string indices. This option can be used to enable non-numeric
     * array indices addressing.
     */
    const OPTION_NON_NUMERIC_INDICES = 0x01;
    /**
     * "Native" JSON arrays have consistent indices, so allowing to create
     * arbitrary numeric indices will break them. But PHP arrays support
     * arrays with "gaps" between indices, and this option enables writing
     * to array at any index.
     */
    const OPTION_NUMERIC_INDEX_GAPS = 0x02;

    /**
     * Bit-packed options.
     *
     * @var int
     */
    protected $options = 0;

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
        $pointer = LocatorEvaluatorTest::factory()
            ->setLocator($this->getLocator())
            ->setData($this->getData());
        if ($this->hasOption(self::OPTION_NON_NUMERIC_INDICES)) {
            $pointer->allowNonNumericIndices();
        }
        return $pointer
            ->evaluate()
            ->getResult();

    }

    public function &read()
    {
        $pointer = LocatorEvaluatorRead::factory()
            ->setLocator($this->getLocator())
            ->setData($this->getData());
        if ($this->hasOption(self::OPTION_NON_NUMERIC_INDICES)) {
            $pointer->allowNonNumericIndices();
        }
        return $pointer
            ->evaluate()
            ->getResult();
    }


    public function write($value)
    {
        $pointer = LocatorEvaluatorWrite::factory()
            ->setLocator($this->getLocator())
            ->setData($this->getData())
            ->setValue($value);
        if ($this->hasOption(self::OPTION_NON_NUMERIC_INDICES)) {
            $pointer->allowNonNumericIndices();
        }
        if ($this->hasOption(self::OPTION_NUMERIC_INDEX_GAPS)) {
            $pointer->allowNumericIndexGaps();
        }
        $pointer->evaluate();
        return $this;
    }


    public function setOptions(int $options)
    {
        $this->options = $options;
        return $this;
    }


    protected function hasOption(int $option)
    {
        return $option == ($option & $this->options);
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
