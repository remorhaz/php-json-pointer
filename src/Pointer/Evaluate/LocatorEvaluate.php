<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator;
use Remorhaz\JSONPointer\Locator\Reference;

/**
 * Abstract JSON Pointer evaluator.
 *
 * @package JSONPointer
 */
abstract class LocatorEvaluate
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

    protected $nonNumericIndices = false;

    /**
     * @var ReferenceEvaluate|null
     */
    protected $referenceEvaluate;


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
     * @throws LogicException
     */
    protected function getLocator()
    {
        if (null === $this->locator) {
            throw new LogicException("Locator is not set in evaluator");
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
     * @throws LogicException
     */
    protected function &getData()
    {
        if (!$this->isDataSet) {
            throw new LogicException("Data is not set in evaluator");
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
     * @throws LogicException
     */
    public function &getResult()
    {
        if (!$this->isResultSet) {
            throw new LogicException("Evaluation result is not set");
        }
        return $this->result;
    }


    public function allowNonNumericIndices()
    {
        $this->nonNumericIndices = true;
        return $this;
    }


    public function forbidNonNumericIndices()
    {
        $this->nonNumericIndices = false;
        return $this;
    }


    /**
     * Performs the evaluation.
     *
     * @return $this
     * @throws LogicException
     */
    public function perform()
    {
        $this->cursor = &$this->getData();
        $this->resetResult();
        foreach ($this->getLocator()->getReferenceList() as $reference) {
            $this->processReference($reference);
            if ($this->isResultSet) {
                break;
            }
        }
        if (!$this->isResultSet) {
            $this->processCursor();
        }
        if (!$this->isResultSet) {
            throw new LogicException("Data evaluation failed");
        }
        return $this;
    }


    /**
     * @param Reference $reference
     * @return ReferenceEvaluate
     */
    abstract protected function createReferenceEvaluate(Reference $reference);


    protected function setupReferenceEvaluate(Reference $reference)
    {
        $this->referenceEvaluate = $this
            ->createReferenceEvaluate($reference)
            ->setReference($reference)
            ->setData($this->cursor);
        return $this;
    }


    /**
     * @return ReferenceEvaluate
     * @throws LogicException
     */
    protected function getReferenceEvaluate()
    {
        if (null === $this->referenceEvaluate) {
            throw new LogicException("Reference evaluator is not set in locator evaluator");
        }
        return $this->referenceEvaluate;
    }


    /**
     * @param Reference $reference
     * @return $this
     * @throws EvaluateException
     */
    protected function processReference(Reference $reference)
    {
        try {
            $this->processReferenceEvaluate($reference);
        } catch (EvaluateException $e) {
            throw new EvaluateException(
                "Error evaluating data for path '{$reference->getPath()}': {$e->getMessage()}",
                null,
                $e
            );
        }
        return $this;
    }


    protected function processReferenceEvaluate(Reference $reference)
    {
        $this
            ->setupReferenceEvaluate($reference)
            ->getReferenceEvaluate()
            ->perform();
        $this->cursor = &$this
            ->getReferenceEvaluate()
            ->getData();
        $isReferenceResultSet = $this
            ->getReferenceEvaluate()
            ->isResultSet();
        if ($isReferenceResultSet) {
            $referenceResult = &$this
                ->getReferenceEvaluate()
                ->getResult();
            $this->setResult($referenceResult);
        }
        return $this;
    }


    abstract protected function processCursor();
}
