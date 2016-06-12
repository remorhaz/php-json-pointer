<?php

namespace Remorhaz\JSONPointer;

class Pointer
{

    /**
     * By default, RFC-6901 allows addressing only numeric indices in array,
     * because JSON arrays can have only numeric indices. But PHP arrays can
     * have string indices. This option can be used to enable non-numeric
     * array indices addressing.
     */
    const OPTION_NON_NUMERIC_ARRAY_INDICES = 0x01;

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
    public function setText($text)
    {
        if ($this->text !== (string) $text) {
            $this->text = (string) $text;
            $this->locator = null;
        }
        return $this;
    }


    /**
     * Returns JSON Pointer string.
     *
     * @return string
     * @throws Pointer\LogicException
     */
    public function getText()
    {
        if (null === $this->text) {
            throw new Pointer\LogicException("JSON Pointer text is not set");
        }
        return $this->text;
    }


    public function setData(&$data)
    {
        $this->data = &$data;
        $this->isDataSet = true;
        return $this;
    }


    public function &getData()
    {
        if (!$this->isDataSet) {
            throw new Pointer\RuntimeException("Data for evaluation is not set");
        }
        return $this->data;
    }


    public function unsetData()
    {
        unset($this->data);
        $this->isDataSet = false;
        return $this;
    }


    public function test()
    {
        return Pointer\Evaluate\Test::factory()
            ->setAllowedNonNumericArrayIndices($this->hasOption(self::OPTION_NON_NUMERIC_ARRAY_INDICES))
            ->setLocator($this->getLocator())
            ->setData($this->getData())
            ->perform()
            ->getResult();
    }

    public function &read()
    {
        return Pointer\Evaluate\Read::factory()
            ->setAllowedNonNumericArrayIndices($this->hasOption(self::OPTION_NON_NUMERIC_ARRAY_INDICES))
            ->setLocator($this->getLocator())
            ->setData($this->getData())
            ->perform()
            ->getResult();
    }


    public function write($value)
    {
        Pointer\Evaluate\Write::factory()
            ->setAllowedNonNumericArrayIndices($this->hasOption(self::OPTION_NON_NUMERIC_ARRAY_INDICES))
            ->setLocator($this->getLocator())
            ->setData($this->getData())
            ->setValue($value)
            ->perform();
        return $this;
    }


    public function setOptions($options)
    {
        $this->options = (int) $options;
        return $this;
    }


    protected function hasOption($option)
    {
        return $option == ($option & $this->options);
    }


    /**
     * Returns parser object.
     *
     * @return Parser
     */
    protected function getParser()
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
    protected function getLocator()
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
