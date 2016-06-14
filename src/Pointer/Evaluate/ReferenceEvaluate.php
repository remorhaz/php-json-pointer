<?php

namespace Remorhaz\JSONPointer\Pointer\Evaluate;

use Remorhaz\JSONPointer\Locator\Reference;

abstract class ReferenceEvaluate
{


    /**
     * Evaluating reference.
     *
     * @var Reference|null
     */
    protected $reference;

    /**
     * Data at cursor.
     *
     * @var mixed
     */
    protected $data;

    protected $result;

    protected $isResultSet;

    /**
     * Flag of having data set.
     *
     * @var bool
     */
    protected $isDataSet = false;

    abstract protected function doesExist();

    abstract protected function performExisting();

    abstract protected function performNonExisting();


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
     * Sets reference for evaluation.
     *
     * @param Reference $reference
     * @return $this
     */
    public function setReference(Reference $reference)
    {
        $this->reference = $reference;
        return $this;
    }


    /**
     * Returns reference for evaluation.
     *
     * @return Reference
     */
    protected function getReference()
    {
        if (null === $this->reference) {
            throw new LogicException("Reference is not set in reference evaluator");
        }
        return $this->reference;
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
            throw new LogicException("Data is not set in reference evaluator");
        }
        return $this->data;
    }
    
    
    public function isResultSet()
    {
        return $this->isResultSet;
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
     * Returns evaluation result.
     *
     * @return mixed
     * @throws LogicException
     */
    public function &getResult()
    {
        if (!$this->isResultSet()) {
            throw new LogicException("Evaluation result is not set");
        }
        return $this->result;
    }


    /**
     * Performs reference evaluation.
     *
     * @return $this
     */
    public function perform()
    {
        return $this->doesExist()
            ? $this->performExisting()
            : $this->performNonExisting();
    }
}
