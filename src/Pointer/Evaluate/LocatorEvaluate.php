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
    protected $dataCursor;

    /**
     * Evaluation result.
     *
     * @var mixed
     */
    protected $result;

    /**
     * @var ReferenceEvaluate|null
     */
    protected $referenceEvaluate;

    /**
     * @var ReferenceEvaluateFactory|null
     */
    protected $referenceEvaluateFactory;


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
        $this
            ->getReferenceEvaluateFactory()
            ->allowNonNumericIndices();
        return $this;
    }


    public function forbidNonNumericIndices()
    {
        $this
            ->getReferenceEvaluateFactory()
            ->forbidNonNumericIndices();
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
        $this->dataCursor = &$this->getData();
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
     * @return ReferenceEvaluateFactory
     */
    abstract protected function createReferenceEvaluateFactory();


    /**
     * @return ReferenceEvaluateFactory
     */
    protected function getReferenceEvaluateFactory()
    {
        if (null === $this->referenceEvaluateFactory) {
            $this->referenceEvaluateFactory = $this->createReferenceEvaluateFactory();
        }
        return $this->referenceEvaluateFactory;
    }


    protected function setupReferenceEvaluateFactory(Reference $reference)
    {
        $this
            ->getReferenceEvaluateFactory()
            ->setReference($reference)
            ->setDataCursor($this->dataCursor);
        return $this;
    }


    protected function setEvaluateForReference(Reference $reference)
    {
        $this->referenceEvaluate = $this
            ->setupReferenceEvaluateFactory($reference)
            ->getReferenceEvaluateFactory()
            ->createEvaluate();
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
            ->setEvaluateForReference($reference)
            ->getReferenceEvaluate()
            ->perform();
        $this->dataCursor = &$this
            ->getReferenceEvaluate()
            ->getDataCursor();
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
