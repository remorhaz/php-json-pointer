<?php

namespace Remorhaz\JSONPointer\Pointer;

use Remorhaz\JSONPointer\Locator;
use Remorhaz\JSONPointer\Locator\Reference;

/**
 * Abstract JSON Pointer evaluator.
 *
 * @package JSONPointer
 */
abstract class Evaluate
{

    /**
     * Locator object.
     *
     * @var Locator|null
     */
    protected $locator;

    /**
     * Data for evaluation.
     *
     * @var mixed
     */
    protected $data;

    /**
     * Data setup flag.
     *
     * @var bool
     */
    protected $isDataSet = false;

    /**
     * Result setup flag.
     *
     * @var bool
     */
    protected $isResultSet = false;

    /**
     * Link to data for the reference being evaluated..
     *
     * @var mixed
     */
    protected $cursor;

    /**
     * Evaluation result.
     *
     * @var mixed
     */
    protected $result;

    protected $nonNumericArrayIndices = false;


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
     * Sets locator object.
     *
     * @param Locator $locator
     * @return $this
     */
    public function setLocator(Locator $locator)
    {
        $this->locator = $locator;
        return $this;
    }


    /**
     * Returns locator object.
     *
     * @return Locator
     * @throws Evaluate\LogicException
     */
    protected function getLocator()
    {
        if (null === $this->locator) {
            throw new Evaluate\LogicException("Locator is not set in evaluator");
        }
        return $this->locator;
    }


    /**
     * Sets data for evaluation.
     *
     * @param mixed $data
     * @return $this
     */
    public function setData(&$data)
    {
        $this->data = &$data;
        $this->isDataSet = true;
        return $this;
    }


    /**
     * Returns data for evaluation.
     *
     * @return mixed
     * @throws Evaluate\LogicException
     */
    protected function &getData()
    {
        if (!$this->isDataSet) {
            throw new Evaluate\LogicException("Data is not set in evaluator");
        }
        return $this->data;
    }


    /**
     * Sets evaluation result.
     *
     * @param mixed $result
     * @return $this
     */
    protected function setResult(&$result)
    {
        $this->result = &$result;
        $this->isResultSet = true;
        return $this;
    }


    /**
     * Resets evaluation result.
     *
     * @return $this
     */
    protected function resetResult()
    {
        unset($this->result);
        $this->isResultSet = false;
        return $this;
    }


    /**
     * Returns evaluation result.
     *
     * @return mixed
     * @throws Evaluate\LogicException
     */
    public function &getResult()
    {
        if (!$this->isResultSet) {
            throw new Evaluate\LogicException("Evaluation result is not set");
        }
        return $this->result;
    }


    public function setNonNumericArrayIndices($allow = true)
    {
        if (!is_bool($allow)) {
            throw new Evaluate\InvalidArgumentException("Non-numeric array indices allowance flag must be boolean");
        }
        $this->nonNumericArrayIndices = $allow;
        return $this;
    }

    /**
     * Performs the evaluation.
     *
     * @return $this
     * @throws Evaluate\LogicException
     */
    public function perform()
    {
        $this->cursor = &$this->getData();
        $this->resetResult();
        try {
            foreach ($this->getLocator()->getReferenceList() as $reference) {
                $this->processReference($reference);
                if ($this->isResultSet) {
                    break;
                }
            }
            if (!$this->isResultSet) {
                $this->processCursor();
            }
        } finally {
            unset($this->cursor);
        }
        if (!$this->isResultSet) {
            throw new Evaluate\LogicException("Data evaluation failed");
        }
        return $this;
    }


    protected function processReference(Reference $reference)
    {
        try {
            if (is_object($this->cursor)) {
                $this->processObjectProperty($reference);
            } elseif (is_array($this->cursor)) {
                $this->processArrayIndex($reference);
            } else {
                throw new Evaluate\EvaluateException("Accessing non-structured data with reference");
            }
        } catch (Evaluate\EvaluateException $e) {
            throw new Evaluate\EvaluateException(
                "Error evaluating data for path '{$reference->getPath()}': {$e->getMessage()}",
                null,
                $e
            );
        }
        return $this;
    }


    abstract protected function processCursor();


    protected function processObjectProperty(Reference $reference)
    {
        return property_exists($this->cursor, $reference->getValue())
            ? $this->processExistingObjectProperty($reference)
            : $this->processNonExistingObjectProperty($reference);
    }


    protected function processExistingObjectProperty(Reference $reference)
    {
        $this->cursor = &$this->cursor->{$reference->getValue()};
        return $this;
    }


    abstract protected function processNonExistingObjectProperty(Reference $reference);


    protected function processArrayIndex(Reference $reference)
    {
        switch ($reference->getType()) {
            case $reference::TYPE_NEXT_INDEX:
                return $this->processNextArrayIndex($reference);

            case $reference::TYPE_INDEX:
                return $this->processNumericArrayIndex($reference);

            default:
                return $this->nonNumericArrayIndices
                    ? $this->processAllowedNonNumericArrayIndex($reference)
                    : $this->processNotAllowedNonNumericArrayIndex($reference);
        }
    }


    protected function processNumericArrayIndex(Reference $reference)
    {
        $index = (int) $reference->getValue();
        return array_key_exists($index, $this->cursor)
            ? $this->processExistingArrayIndex($reference)
            : $this->processNonExistingArrayIndex($reference);
    }


    /**
     * @param Reference $reference
     * @return $this
     * @throws Evaluate\EvaluateException
     */
    protected function processNotAllowedNonNumericArrayIndex(Reference $reference)
    {
        throw new Evaluate\EvaluateException(
            "Accessing non-numeric index '{$reference->getValue()}' in array"
        );
    }


    protected function processAllowedNonNumericArrayIndex(Reference $reference)
    {
        $index = $reference->getValue();
        return array_key_exists($index, $this->cursor)
            ? $this->processExistingArrayIndex($reference)
            : $this->processNonExistingArrayIndex($reference);
    }


    abstract protected function processNextArrayIndex(Reference $reference);


    protected function processExistingArrayIndex(Reference $reference)
    {
        $this->cursor = &$this->cursor[$this->getArrayIndex($reference)];
        return $this;
    }


    abstract protected function processNonExistingArrayIndex(Reference $reference);


    protected function getArrayIndex(Reference $reference)
    {
        return $reference->getType() == $reference::TYPE_INDEX
            ? (int) $reference->getValue()
            : $reference->getValue();
    }
}
